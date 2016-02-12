<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Tests\Http;

use Http\Client\Exception\TransferException;
use Http\Client\HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use Mekras\Obereg\Core\Cache\Cache;
use Mekras\Obereg\Core\Policy\Inbound\DefaultInboundPolicy;
use Mekras\Obereg\Http\HttpGateway;
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
     *
     */
    public function testUnsafeTransfer()
    {
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        /** @var RequestInterface $request */

        $expectedResponse = $this->getMockForAbstractClass(ResponseInterface::class);
        /** @var ResponseInterface $expectedResponse */

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::once())->method('sendRequest')->with($request)
            ->willReturn($expectedResponse);
        /** @var HttpClient $httpClient */

        $gw = new HttpGateway(
            'foo',
            $httpClient,
            new GuzzleMessageFactory(),
            new GuzzleStreamFactory()
        );

        $actualResponse = $gw->sendRequest($request);

        static::assertSame($actualResponse, $expectedResponse);
    }

    /**
     * @expectedException \Mekras\Obereg\Core\Exception\InboundTransferException
     */
    public function testInboundCacheEmptyNoDefault()
    {
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        /** @var RequestInterface $request */

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::once())->method('sendRequest')->with($request)
            ->willThrowException(new TransferException());
        /** @var HttpClient $httpClient */

        $gw = new HttpGateway(
            'foo',
            $httpClient,
            new GuzzleMessageFactory(),
            new GuzzleStreamFactory()
        );
        $gw->sendRequest($request);
    }

    /**
     *
     */
    public function testInboundCacheEmptyDefault()
    {
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        /** @var RequestInterface $request */

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::once())->method('sendRequest')->with($request)
            ->willThrowException(new TransferException());
        /** @var HttpClient $httpClient */

        $expectedResponse = $this->getMockForAbstractClass(ResponseInterface::class);
        /** @var ResponseInterface $expectedResponse */

        $gw = new HttpGateway(
            'foo',
            $httpClient,
            new GuzzleMessageFactory(),
            new GuzzleStreamFactory()
        );
        $gw->setInboundPolicy(new DefaultInboundPolicy($expectedResponse));

        $actualResponse = $gw->sendRequest($request);

        static::assertSame($actualResponse, $expectedResponse);
    }

    /**
     *
     */
    public function testInboundCached()
    {
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        /** @var RequestInterface $request */

        $httpClient = $this->getMockForAbstractClass(HttpClient::class);
        $httpClient->expects(static::once())->method('sendRequest')->with($request)
            ->willThrowException(new TransferException());
        /** @var HttpClient $httpClient */

        $cache = $this->getMockForAbstractClass(Cache::class);
        $cache->expects(static::once())->method('get')->with('foo')->willReturn(
            "RESPONSE\r\n" .
            "Content-type: application/json\r\n" .
            "\r\n" .
            'Foo'
        );
        /** @var Cache $cache */

        $gw = new HttpGateway(
            'foo',
            $httpClient,
            new GuzzleMessageFactory(),
            new GuzzleStreamFactory()
        );
        $gw->setCache($cache);

        $response = $gw->sendRequest($request);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('Foo', $response->getBody()->getContents());
    }

    /**
     *
     * /
     * public function testOutboundEnqueue()
     * {
     * $request = $this->getMockForAbstractClass(RequestInterface::class);
     * /** @var RequestInterface $request * /
     *
     * $httpClient = $this->getMockForAbstractClass(HttpClient::class);
     * $httpClient->expects(static::once())->method('sendRequest')->with($request)
     * ->willThrowException(new TransferException());
     * /** @var HttpClient $httpClient * /
     *
     * $gw = new HttpGateway('foo', $httpClient);
     * $gw->setOutboundPolicy();
     *
     * $gw->sendRequest($request);
     *
     * static::assertSame($actualResponse, $expectedResponse);
     * }*/
}
