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
use Mekras\Obereg\Exception\InboundTransferException;
use Mekras\Obereg\Exception\SerializeException;
use Mekras\Obereg\Exception\UnserializeException;
use Mekras\Obereg\Gateway;
use Mekras\Obereg\Storage\Storage;
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
     * @param Storage        $storage    Data storage.
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
        Storage $storage,
        HttpClient $httpClient,
        MessageFactory $messageFactory,
        StreamFactory $streamFactory
    ) {
        parent::__construct($id, $storage, new HttpDataSerializer($messageFactory, $streamFactory));
        $this->httpClient = $httpClient;
    }

    /**
     * Sends a PSR-7 request.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     * @throws \Http\Client\Exception
     * @throws \InvalidArgumentException
     * @throws InboundTransferException
     * @throws \InvalidArgumentException
     * @throws SerializeException
     * @throws UnserializeException
     *
     * @since 1.0
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->transfer($request);
    }

    /**
     * Send outbound data and return inbound one.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     * @throws \Http\Client\Exception
     * @throws \InvalidArgumentException
     * @throws InboundTransferException
     * @throws \InvalidArgumentException
     * @throws SerializeException
     * @throws UnserializeException
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
            $this->sendLater($request);
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
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    protected function checkArgumentType($method, $argNum, $expected, $actual)
    {
        if ($expected !== gettype($actual) && !is_a($actual, $expected)) {
            throw new \InvalidArgumentException($method, $argNum, $expected, $actual);
        }
    }
}
