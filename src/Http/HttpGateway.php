<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Http;

use Http\Client\Exception\TransferException;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Http\Message\StreamFactory;
use Mekras\Obereg\Core\Exception\InboundTransferException;
use Mekras\Obereg\Core\Gateway;
use Mekras\Types\Exception\InvalidArgumentTypeException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP gateway.
 *
 * @api
 * @since 1.0
 */
class HttpGateway extends Gateway implements HttpClient
{
    /**
     * HTTP client
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * HttpGateway constructor.
     *
     * @param string         $id         Permanent unique gateway ID.
     * @param HttpClient     $httpClient HTTP client.
     * @param MessageFactory $messageFactory
     * @param StreamFactory  $streamFactory
     *
     * @throws \InvalidArgumentException If $id is empty
     *
     * @since 1.0
     */
    public function __construct(
        $id,
        HttpClient $httpClient,
        MessageFactory $messageFactory,
        StreamFactory $streamFactory
    ) {
        parent::__construct($id);
        $this->setSerializer(new HttpDataSerializer($messageFactory, $streamFactory));
        $this->httpClient = $httpClient;
    }

    /**
     * Sends a PSR-7 request.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @since 1.0
     */
    public function sendRequest(RequestInterface $request)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return $this->transfer($request);
    }

    /**
     * Send outbound data and return inbound one.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws InboundTransferException
     * @throws InvalidArgumentTypeException
     * @throws \Http\Client\Exception
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    protected function transfer($request)
    {
        $this->checkArgumentType(__METHOD__, 1, RequestInterface::class, $request);
        try {
            /* Try to perform real request and get actual response */
            $response = $this->httpClient->sendRequest($request);
        } catch (TransferException $e) {
            /* In case of failure pushing request to outbound channel */
            //$this->getQueue()->enqueue($request);
            $response = $this->getCachedData($request);
        }
        return $response;
    }

    /**
     * Return hash for a given data.
     *
     * @param mixed $data
     *
     * @return string Data hash.
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    protected function getDataHash($data)
    {
        $this->checkArgumentType(__METHOD__, 1, RequestInterface::class, $data);
        return sha1((string) $data->getUri());
    }

    /**
     * Check argument type.
     *
     * @param string $method   Use __METHOD__ magic constant.
     * @param int    $argNum   Argument number.
     * @param string $expected Expected argument class or type.
     * @param mixed  $actual   Actual argument value.
     *
     * @return void
     *
     * @throws InvalidArgumentTypeException
     *
     * @since 1.0
     */
    protected function checkArgumentType($method, $argNum, $expected, $actual)
    {
        if ($expected !== gettype($actual) && !is_a($actual, $expected)) {
            throw new InvalidArgumentTypeException($method, $argNum, $expected, $actual);
        }
    }
}
