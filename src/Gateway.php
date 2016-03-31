<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg;

use Mekras\Obereg\Exception\InboundTransferException;
use Mekras\Obereg\Exception\SerializeException;
use Mekras\Obereg\Exception\UnserializeException;
use Mekras\Obereg\Policy\Inbound\DefaultInboundPolicy;
use Mekras\Obereg\Policy\Inbound\InboundPolicy;
use Mekras\Obereg\Policy\Outbound\DefaultOutboundPolicy;
use Mekras\Obereg\Policy\Outbound\OutboundPolicy;
use Mekras\Obereg\Storage\Storage;

/**
 * Abstract gateway.
 *
 * @api
 * @since 1.0
 */
abstract class Gateway
{
    /**
     * Gateway ID
     *
     * @var string
     */
    private $id;

    /**
     * Data storage.
     *
     * @var Storage
     */
    private $storage;

    /**
     * Data serializer
     *
     * @var DataSerializer
     */
    private $serializer;

    /**
     * Inbound transfers policy
     *
     * @var InboundPolicy
     */
    private $inboundPolicy;

    /**
     * Outbound transfers policy
     *
     * @var OutboundPolicy
     */
    private $outboundPolicy;

    /**
     * Create new Gateway.
     *
     * @param string              $id         Immutable unique gateway ID.
     * @param Storage             $storage    Data storage.
     * @param DataSerializer|null $serializer Data serializer.
     *
     * @throws \InvalidArgumentException If $id is empty
     *
     * @since 1.0
     */
    public function __construct($id, Storage $storage, DataSerializer $serializer = null)
    {
        $id = (string) $id;
        if ('' === $id) {
            throw new \InvalidArgumentException('Gateway ID can not be empty');
        }
        $this->id = $id;
        $this->storage = $storage;
        $this->serializer = $serializer ?: new DataSerializer();
        $this->inboundPolicy = new DefaultInboundPolicy();
        $this->outboundPolicy = new DefaultOutboundPolicy();
    }

    /**
     * Return permanent unique gateway ID.
     *
     * @return string
     *
     * @since 1.0
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Resend all queued data
     *
     * @return void
     *
     * @throws UnserializeException
     *
     * @since 1.0
     */
    public function runQueue()
    {
        while (true) {
            $container = $this->getStorage()->dequeue($this->id);
            if (null === $container) {
                break;
            }

            if ($this->getOutboundPolicy()->isResendAllowed($container)) {
                $raw = $container->getData();
                $data = $this->getSerializer()->unserialize($raw);
                $this->transfer($data);
            }
        }
    }

    /**
     * Return inbound transfers policy
     *
     * @return InboundPolicy
     *
     * @since 1.0
     */
    public function getInboundPolicy()
    {
        return $this->inboundPolicy;
    }

    /**
     * Set inbound transfers policy
     *
     * @param InboundPolicy $policy
     *
     * @since 1.0
     */
    public function setInboundPolicy(InboundPolicy $policy)
    {
        $this->inboundPolicy = $policy;
    }

    /**
     * Return outbound transfers policy
     *
     * @return OutboundPolicy
     *
     * @since 1.0
     */
    public function getOutboundPolicy()
    {
        return $this->outboundPolicy;
    }

    /**
     * Set outbound transfers policy
     *
     * @param OutboundPolicy $policy
     *
     * @since 1.0
     */
    public function setOutboundPolicy(OutboundPolicy $policy)
    {
        $this->outboundPolicy = $policy;
    }

    /**
     * Send outbound data and return inbound one.
     *
     * @param mixed $data
     *
     * @return mixed
     *
     * @since 1.0
     */
    abstract protected function transfer($data);

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
    abstract protected function getDataHash($data);

    /**
     * Return cached or default inbound data
     *
     * @param mixed $request
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @throws InboundTransferException If there is no cached or default response.
     * @throws UnserializeException
     *
     * @since 1.0
     */
    protected function getCachedData($request)
    {
        $hash = $this->getDataHash($request);
        $container = $this->getStorage()->get($this->getId(), $hash);
        if (null !== $container && $this->getInboundPolicy()->isActual($container)) {
            return $this->getSerializer()->unserialize($container->getData());
        }
        $data = $this->getInboundPolicy()->getDefault();
        if (null !== $data) {
            return $data;
        }
        throw new InboundTransferException('There is no cached or default inbound data');
    }

    /**
     * Store request to resend it later
     *
     * @param mixed $request
     *
     * @return void
     *
     * @throws SerializeException If $request can not be serialized.
     *
     * @since 1.0
     */
    protected function sendLater($request)
    {
        $raw = $this->getSerializer()->serialize($request);
        $this->getStorage()->enqueue($this->getId(), $raw);
    }

    /**
     * Return storage.
     *
     * @return Storage
     */
    protected function getStorage()
    {
        return $this->storage;
    }

    /**
     * Return current serializer.
     *
     * @return DataSerializer
     */
    protected function getSerializer()
    {
        return $this->serializer;
    }
}
