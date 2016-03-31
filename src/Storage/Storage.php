<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Storage;

/**
 * Storage interface.
 *
 * @api
 * @since 1.0
 */
interface Storage
{
    /**
     * Fetch next data item.
     *
     * @param string $gatewayId The gateway ID.
     *
     * @return DataContainer|null The data container or null if queue is empty.
     *
     * @since 1.0
     */
    public function dequeue($gatewayId);

    /**
     * Push data to queue.
     *
     * @param string $gatewayId The gateway ID.
     * @param string $data      The data to be stored.
     *
     * @return void
     *
     * @since 1.0
     */
    public function enqueue($gatewayId, $data);

    /**
     * Fetch cached data from storage.
     *
     * @param string $gatewayId The gateway ID.
     * @param string $hash      The data hash.
     *
     * @return DataContainer|null The data container or null if no data available
     *
     * @since 1.0
     */
    public function get($gatewayId, $hash);

    /**
     * Store data.
     *
     * @param string $gatewayId The gateway ID.
     * @param string $hash      The data hash.
     * @param string $data      The data to be stored.
     *
     * @return void
     *
     * @since 1.0
     */
    public function put($gatewayId, $hash, $data);
}
