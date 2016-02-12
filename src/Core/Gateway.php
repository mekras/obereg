<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Core;

use Mekras\Obereg\Core\Cache\Cache;
use Mekras\Obereg\Core\Cache\NullCache;
use Mekras\Obereg\Core\Exception\InboundTransferException;
use Mekras\Obereg\Core\Policy\Inbound\DefaultInboundPolicy;
use Mekras\Obereg\Core\Policy\Inbound\InboundPolicy;
use Mekras\Obereg\Core\Policy\Outbound\OutboundPolicy;
use Mekras\Obereg\Queue\Queue;

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
     * Inbound transfers cache.
     *
     * @var Cache
     */
    private $cache;

    /**
     * Inbound transfers policy
     *
     * @var InboundPolicy|null
     *
     * @since 1.0
     */
    private $inboundPolicy = null;

    /**
     * Outbound transfers queue.
     *
     * @var Queue
     */
    private $queue;

    /**
     * Data serializer
     *
     * @var DataSerializer
     */
    private $serializer;

    /**
     * Outbound transfers policy
     *
     * @var OutboundPolicy|null
     * @since 1.0
     */
    private $outboundPolicy = null;

    /**
     * Create new Gateway.
     *
     * @param string $id Immutable unique gateway ID.
     *
     * @throws \InvalidArgumentException If $id is empty
     *
     * @since 1.0
     */
    public function __construct($id)
    {
        $id = (string) $id;
        if ('' === $id) {
            throw new \InvalidArgumentException('Gateway ID can not be empty');
        }
        $this->id = $id;
        $this->cache = new NullCache();
        $this->inboundPolicy = new DefaultInboundPolicy();
        //$this->queue = new NullQueue();
        $this->serializer = new DataSerializer();
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
     * Return inbound transfers cache.
     *
     * @return Cache
     *
     * @since 1.0
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set inbound transfers cache.
     *
     * @param Cache $cache
     *
     * @since 1.0
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
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
     * Set outbound queue
     *
     * @param Queue $queue
     *
     * @since 1.0
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;
        //$this->queue->setId
    }

    /**
     * Return outbound transfers queue.
     *
     * @return Queue|null
     *
     * @since 1.0
     */
    public function getQueue()
    {
        return $this->queue;
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
     * Return current serializer.
     *
     * @return DataSerializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Set new serializer.
     *
     * @param DataSerializer $serializer
     */
    public function setSerializer(DataSerializer $serializer)
    {
        $this->serializer = $serializer;
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
     * @throws InboundTransferException If there is no cached or default response.
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    protected function getCachedData($request)
    {
        if ($this->getCache()) {
            $hash = $this->getDataHash($request);
            $item = $this->getCache()->get($this->getId(), $hash);
            if (null !== $item) {
                return $this->getSerializer()->unserialize($item->get());
            }
        }
        $data = $this->getInboundPolicy()->getDefault();
        if (null !== $data) {
            return $data;
        }
        throw new InboundTransferException('There is no cached or default response');
    }
}
