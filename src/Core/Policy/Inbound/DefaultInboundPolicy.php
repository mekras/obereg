<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Core\Policy\Inbound;

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
     * CachePolicy constructor.
     *
     * @param mixed $default
     *
     * @since 1.0
     */
    public function __construct($default = null)
    {
        $this->default = $default;
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
}
