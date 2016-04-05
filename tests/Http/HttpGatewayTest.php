<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Tests\Http;

use Doctrine\Common\Cache\ArrayCache;
use Http\Client\Exception\TransferException;
use Http\Client\HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use Mekras\Obereg\Doctrine\Storage\DoctrineCacheStorage;
use Mekras\Obereg\Http\HttpGateway;
use Mekras\Obereg\Policy\Inbound\DefaultInboundPolicy;
use Mekras\Obereg\Policy\Outbound\DefaultOutboundPolicy;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for Mekras\Obereg\Http\HttpGateway
 *
 * @ covers Mekras\Obereg\Http\HttpGateway
 */
class HttpGatewayTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testUnsafeTransfer()
    {
        $messageFactory = new GuzzleMessageFactory();

        $request = $messageFactory->createRequest('GET', 'http://example.com');

        $expectedResponse = $this->getMockForAbstractClass(ResponseInterface::class);
        /** @var ResponseInterface $expectedResponse */

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::once())->method('sendRequest')->with($request)
            ->willReturn($expectedResponse);
        /** @var HttpClient $httpClient */

        $storage = new DoctrineCacheStorage(new ArrayCache());

        $gw = new HttpGateway(
            'foo',
            $storage,
            $httpClient,
            $messageFactory,
            new GuzzleStreamFactory()
        );

        $actualResponse = $gw->sendRequest($request);

        static::assertSame($actualResponse, $expectedResponse);
    }

    /**
     * @expectedException \Mekras\Obereg\Exception\InboundTransferException
     *
     * @throws \Exception
     */
    public function testInboundCacheEmptyNoDefault()
    {
        $messageFactory = new GuzzleMessageFactory();

        $request = $messageFactory->createRequest('GET', 'http://example.com');

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::once())->method('sendRequest')->with($request)
            ->willThrowException(new TransferException());
        /** @var HttpClient $httpClient */

        $storage = new DoctrineCacheStorage(new ArrayCache());

        $gw = new HttpGateway(
            'foo',
            $storage,
            $httpClient,
            $messageFactory,
            new GuzzleStreamFactory()
        );
        $gw->sendRequest($request);
    }

    /**
     * @throws \Exception
     */
    public function testInboundCacheEmptyDefault()
    {
        $messageFactory = new GuzzleMessageFactory();

        $request = $messageFactory->createRequest('GET', 'http://example.com');

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::once())->method('sendRequest')->with($request)
            ->willThrowException(new TransferException());
        /** @var HttpClient $httpClient */

        $expectedResponse = $this->getMockForAbstractClass(ResponseInterface::class);
        /** @var ResponseInterface $expectedResponse */

        $storage = new DoctrineCacheStorage(new ArrayCache());

        $gw = new HttpGateway(
            'foo',
            $storage,
            $httpClient,
            $messageFactory,
            new GuzzleStreamFactory()
        );
        $gw->setInboundPolicy(new DefaultInboundPolicy($expectedResponse));

        $actualResponse = $gw->sendRequest($request);

        static::assertSame($expectedResponse, $actualResponse);
    }

    /**
     * @throws \Exception
     */
    public function testInboundCached()
    {
        $messageFactory = new GuzzleMessageFactory();

        $request = $messageFactory->createRequest('GET', 'http://example.com');

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::once())->method('sendRequest')->with($request)
            ->willThrowException(new TransferException());
        /** @var HttpClient $httpClient */

        $storage = new DoctrineCacheStorage(new ArrayCache());
        $storage->put(
            'foo',
            sha1((string) $request->getUri()),
            "RESPONSE\r\n" .
            "Content-type: application/json\r\n" .
            "\r\n" .
            'Foo'
        );

        $gw = new HttpGateway(
            'foo',
            $storage,
            $httpClient,
            $messageFactory,
            new GuzzleStreamFactory()
        );

        $response = $gw->sendRequest($request);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('Foo', $response->getBody()->getContents());
    }

    /**
     * @throws \Exception
     */
    public function testOutboundEnqueue()
    {
        $messageFactory = new GuzzleMessageFactory();

        $request = $messageFactory->createRequest('GET', 'http://example.com');

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::exactly(2))->method('sendRequest')->willReturnCallback(
            function (RequestInterface $req) use ($request) {
                static $it = 0;
                $it++;
                switch ($it) {
                    case 1:
                        throw new TransferException();

                    case 2:
                        \PHPUnit_Framework_Assert::assertEquals($request->getUri(), $req->getUri());
                        return $this->getMockForAbstractClass(ResponseInterface::class);
                }
            }
        );
        /** @var HttpClient $httpClient */

        $storage = new DoctrineCacheStorage(new ArrayCache());

        $gw = new HttpGateway(
            'foo',
            $storage,
            $httpClient,
            $messageFactory,
            new GuzzleStreamFactory()
        );

        $defaultResponse = $this->getMockForAbstractClass(ResponseInterface::class);
        /** @var ResponseInterface $defaultResponse */
        $gw->setInboundPolicy(new DefaultInboundPolicy($defaultResponse));
        $gw->setOutboundPolicy(new DefaultOutboundPolicy(1));

        $response = $gw->sendRequest($request);
        static::assertSame($defaultResponse, $response);

        sleep(2);
        $gw->runQueue();
    }
}
