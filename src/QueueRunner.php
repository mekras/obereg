<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg;

/**
 * Queue runner.
 *
 * @since 1.0
 */
class QueueRunner
{
    /**
     * Gateway registry.
     *
     * @var Gateway[]
     */
    private $gateways = [];

    /**
     * Register gateway.
     *
     * @param Gateway $gateway
     *
     * @link  unregister()
     * @since 1.0
     */
    public function register(Gateway $gateway)
    {
        $this->gateways[$gateway->getId()] = $gateway;
    }

    /**
     * Unregister gateway.
     *
     * @param Gateway $gateway
     *
     * @link  register()
     * @since 1.0
     */
    public function unregister(Gateway $gateway)
    {
        unset($this->gateways[$gateway->getId()]);
    }

    /**
     * Resend all queued data for all registered gateways.
     *
     * @return void
     *
     * @since 1.0
     */
    public function run()
    {
        foreach ($this->gateways as $gateway) {
            $gateway->runQueue();
        }
    }
}
