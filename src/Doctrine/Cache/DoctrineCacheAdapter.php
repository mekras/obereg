<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Doctrine\Cache;

use Mekras\Obereg\Core\Cache\Cache;
use Doctrine\Common\Cache\Cache as DoctrineCache;
use Mekras\Obereg\Core\Cache\CacheItem;
use Mekras\Obereg\Core\Cache\SerializableCacheItem;

/**
 * Adapter for Doctrine Cache.
 *
 * @api
 * @since 1.0
 */
class DoctrineCacheAdapter implements Cache
{
    /**
     * Doctrine cache.
     *
     * @var DoctrineCache
     */
    private $cache;

    /**
     * DoctrineCacheAdapter constructor.
     *
     * @param DoctrineCache $cache Instance of {@link Doctrine\Common\Cache\Cache}
     */
    public function __construct(DoctrineCache $cache)
    {
        $this->cache = $cache;
    }

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
        $item = new SerializableCacheItem($data);
        $this->cache->save($this->createKey($gatewayId, $hash), $item);
    }

    /**
     * Fetch data from cache.
     *
     * @param string $gatewayId The gateway ID.
     * @param string $hash      The data hash.
     *
     * @return CacheItem|null The data container or null if no data available
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
