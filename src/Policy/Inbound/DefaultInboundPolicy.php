<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Policy\Inbound;

use Mekras\Obereg\Storage\DataContainer;

/**
 * Policy that simply caches inbound data
 *
 * @api
 * @since 1.0
 */
class DefaultInboundPolicy implements InboundPolicy
{
    /**
     * Default inbound data
     *
     * @var mixed|null
     */
    private $default = null;

    /**
     * TTL for cached data in seconds.
     *
     * @var int
     */
    private $ttl;

    /**
     * Policy constructor.
     *
     * @param mixed $default Default data.
     * @param int   $ttl     TTL for cached data in seconds (0 — never expires).
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    public function __construct($default = null, $ttl = 0)
    {
        $this->default = $default;
        $this->ttl = (int) $ttl;
        if ($this->ttl < 0) {
            throw new \InvalidArgumentException('TTL can not be negative');
        }
    }

    /**
     * Default value to return if inbound transfer failed and no cached value available.
     *
     * @return mixed
     *
     * @since 1.0
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Return true if stored data is still actual.
     *
     * @param DataContainer $dataContainer
     *
     * @return bool
     *
     * @since 1.0
     */
    public function isActual(DataContainer $dataContainer)
    {
        if (0 === $this->ttl) {
            return true;
        }
        return time() - $dataContainer->getCreated() <= $this->ttl;
    }
}
