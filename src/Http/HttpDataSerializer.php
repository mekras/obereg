<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Http;

use Http\Message\MessageFactory;
use Http\Message\StreamFactory;
use Mekras\Obereg\Core\DataSerializer;
use Mekras\Obereg\Core\Exception\UnserializeException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;

/**
 * HTTP message serializer.
 *
 * Serialize and unserialize instances of Psr\Http\Message\MessageInterface.
 *
 * @api
 * @since 1.0
 */
class HttpDataSerializer extends DataSerializer
{
    const REQUEST = 'REQUEST';
    const RESPONSE = 'RESPONSE';

    /**
     * HTTP message factory
     *
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * Stream factory
     *
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * Create new HTTP data serializer.
     *
     * @param MessageFactory $messageFactory
     * @param StreamFactory  $streamFactory
     */
    public function __construct(MessageFactory $messageFactory, StreamFactory $streamFactory)
    {
        $this->messageFactory = $messageFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Serialize data to binary string.
     *
     * @param MessageInterface $data
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    public function serialize($data)
    {
        if (!$data instanceof MessageInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument 1 of %s should be an instance of %s, %s given',
                    __METHOD__,
                    MessageInterface::class,
                    is_object($data) ? get_class($data) : gettype($data)
                )
            );
        }
        $lines = [];
        if ($data instanceof RequestInterface) {
            $lines[] = self::REQUEST;
            $lines[] = 'HTTP/' . $data->getProtocolVersion() . ' ' . $data->getMethod() . ' ' .
                $data->getUri();
        } else {
            $lines[] = self::RESPONSE;
        }
        foreach ($data->getHeaders() as $name => $values) {
            $lines[] = $name . ': ' . implode(', ', $values);
        }
        $lines[] = '';
        $lines[] = $data->getBody()->getContents();

        return implode("\r\n", $lines);
    }

    /**
     * Unserialize data from binary string.
     *
     * @param string $string
     *
     * @return MessageInterface Unserialized data.
     *
     * @throws UnserializeException If $string can not be unserialized.
     *
     * @since 1.0
     */
    public function unserialize($string)
    {
        $parts = explode("\r\n", $string, 2);
        if (count($parts) !== 2) {
            throw new UnserializeException('Serialized data does not contain message type ID');
        }
        $type = $parts[0];
        $string = $parts[1];
        switch ($type) {
            case self::REQUEST:
                list($part, $string) = explode("\r\n", $string, 2);
                $pattern = "/^HTTP\\/(?P<version>\\d\\.\\d) (?P<method>[A-Z]+) (?P<uri>.+)$/";
                if (preg_match($pattern, $part, $matches) === 0) {
                    throw new UnserializeException(
                        sprintf('Line "%s" not matches HTTP heading format', $part)
                    );
                }
                $message = $this->messageFactory
                    ->createRequest($matches['method'], $matches['uri']);
                $message = $message->withProtocolVersion($matches['version']);
                break;

            case self::RESPONSE:
                $message = $this->messageFactory->createResponse();
                break;

            default:
                throw new UnserializeException;
        }

        /** @var MessageInterface $message */
        list($part, $string) = explode("\r\n\r\n", $string, 2);
        $headers = explode("\r\n", $part);
        foreach ($headers as $header) {
            list($name, $value) = explode(':', $header, 2);
            $name = trim(urldecode($name));
            $value = trim(urldecode($value));
            if ($message->hasHeader($name)) {
                $message = $message->withAddedHeader($name, $value);
            } else {
                $message = $message->withHeader($name, $value);
            }
        }

        $body = $this->streamFactory->createStream($string);
        $message = $message->withBody($body);
        return $message;
    }
}
