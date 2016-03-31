<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Doctrine\Storage;

use Doctrine\Common\Cache\Cache;
use Mekras\Obereg\Storage\DataContainer;
use Mekras\Obereg\Storage\GenericDataContainer;
use Mekras\Obereg\Storage\Storage;

/**
 * Doctrine Cache to Storage adapter.
 *
 * @since 1.0
 */
class DoctrineCacheStorage implements Storage
{
    /**
     * Doctrine cache.
     *
     * @var Cache
     */
    private $cache;

    /**
     * Create new adapter.
     *
     * @param Cache $cache Instance of {@link Doctrine\Common\Cache\Cache}
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Fetch next data item.
     *
     * @param string $gatewayId The gateway ID.
     *
     * @return DataContainer|null The data container or null if queue is empty.
     *
     * @since 1.0
     */
    public function dequeue($gatewayId)
    {
        $key = $this->createKey($gatewayId, '$queue');
        if ($this->cache->contains($key)) {
            $queue = $this->cache->fetch($key);
            $container = array_shift($queue);
            $this->cache->save($key, $queue);
            return $container;
        }
        return null;
    }

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
    public function enqueue($gatewayId, $data)
    {
        $key = $this->createKey($gatewayId, '$queue');
        $queue = [];
        if ($this->cache->contains($key)) {
            $queue = $this->cache->fetch($key);
        }

        $queue[] = new GenericDataContainer($data);
        $this->cache->save($key, $queue);
    }

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
    public function get($gatewayId, $hash)
    {
        $key = $this->createKey($gatewayId, $hash);
        if (!$this->cache->contains($key)) {
            return null;
        }

        return $this->cache->fetch($key);
    }

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
    public function put($gatewayId, $hash, $data)
    {
        $container = new GenericDataContainer($data);
        $this->cache->save($this->createKey($gatewayId, $hash), $container);
    }

    /**
     * Create internal cache entry key.
     *
     * @param string $gatewayId The gateway ID.
     * @param string $hash      The data hash.
     *
     * @return string
     */
    private function createKey($gatewayId, $hash)
    {
        return $gatewayId . '.' . $hash;
    }
}
