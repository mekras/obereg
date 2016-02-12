<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Core\Cache;

/**
 * Null object cache.
 *
 * @api
 * @since 1.0
 */
class NullCache implements Cache
{
    /**
     * Store data.
     *
     * @param string $gatewayId The gateway ID.
     * @param string $hash      The data hash.
     * @param string $data      The data to store as a string.
     *
     * @return void
     *
     * @since 1.0
     */
    public function put($gatewayId, $hash, $data)
    {
        // Do nothing
    }

    /**
     * Fetch data from cache.
     *
     * @param string $gatewayId The gateway ID.
     * @param string $hash      The data hash.
     *
     * @return Container|null The data container or null if no data available
     *
     * @since 1.0
     */
    public function get($gatewayId, $hash)
    {
        return null;
    }
}
