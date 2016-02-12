<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Tests\Http;

use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use Mekras\Obereg\Http\HttpDataSerializer;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Тесты класса Mekras\Obereg\Http\HttpDataSerializer
 *
 * @covers Mekras\Obereg\Http\HttpDataSerializer
 */
class HttpDataSerializerTest extends TestCase
{
    /**
     * Test request serialization
     */
    public function testSerializeRequest()
    {
        $factory = new GuzzleMessageFactory();
        $serializer = new HttpDataSerializer($factory, new GuzzleStreamFactory());

        $request = $factory->createRequest(
            'GET',
            'http://example.com/',
            ['Accept' => 'application/json'],
            'Foo'
        );

        $string = $serializer->serialize($request);
        static::assertEquals(
            "REQUEST\r\n" .
            "HTTP/1.1 GET http://example.com/\r\n" .
            "Host: example.com\r\n" .
            "Accept: application/json\r\n" .
            "\r\n" .
            'Foo',
            $string
        );
    }

    /**
     * Test request unserialization
     */
    public function testUnserializeRequest()
    {
        $factory = new GuzzleMessageFactory();
        $serializer = new HttpDataSerializer($factory, new GuzzleStreamFactory());

        $request = $serializer->unserialize(
            "REQUEST\r\n" .
            "HTTP/1.1 GET http://example.com/\r\n" .
            "Host: example.com\r\n" .
            "Accept: application/json\r\n" .
            "\r\n" .
            'Foo'
        );
        /** @var RequestInterface $request */

        static::assertInstanceOf(RequestInterface::class, $request);
        static::assertEquals('1.1', $request->getProtocolVersion());
        static::assertEquals('GET', $request->getMethod());
        static::assertEquals('http://example.com/', $request->getUri());
        static::assertEquals('Foo', $request->getBody()->getContents());
    }

    /**
     * Test rersponse serialization
     */
    public function testSerializeResponse()
    {
        $factory = new GuzzleMessageFactory();
        $serializer = new HttpDataSerializer($factory, new GuzzleStreamFactory());

        $response = $factory->createResponse(
            200,
            null,
            ['Content-type' => 'application/json'],
            'Foo'
        );

        $string = $serializer->serialize($response);
        static::assertEquals(
            "RESPONSE\r\n" .
            "Content-type: application/json\r\n" .
            "\r\n" .
            'Foo',
            $string
        );
    }

    /**
     * Test response unserialization
     */
    public function testUnserializeResponse()
    {
        $factory = new GuzzleMessageFactory();
        $serializer = new HttpDataSerializer($factory, new GuzzleStreamFactory());

        $response = $serializer->unserialize(
            "RESPONSE\r\n" .
            "Content-type: application/json\r\n" .
            "\r\n" .
            'Foo'
        );
        /** @var ResponseInterface $response */

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('application/json', $response->getHeaderLine('Content-type'));
        static::assertEquals('Foo', $response->getBody()->getContents());
    }
}
