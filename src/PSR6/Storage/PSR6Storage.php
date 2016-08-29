<?php
/**
 * PHP fault tolerance library.
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\PSR6\Storage;

use Mekras\Obereg\Storage\DataContainer;
use Mekras\Obereg\Storage\GenericDataContainer;
use Mekras\Obereg\Storage\Storage;
use Psr\Cache\CacheItemPoolInterface;

/**
 * PSR-6 Storage adapter.
 *
 * @see   http://www.php-fig.org/psr/psr-6/
 * @since 1.0
 */
class PSR6Storage implements Storage
{
    /**
     * Cache.
     *
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * Create new adapter.
     *
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
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
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    public function dequeue($gatewayId)
    {
        $key = $this->createKey($gatewayId, '_queue');
        if ($this->cache->hasItem($key)) {
            $item = $this->cache->getItem($key);
            $queue = $item->get();
            $container = array_shift($queue);
            $item->set($queue);
            $this->cache->save($item);

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
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    public function enqueue($gatewayId, $data)
    {
        $key = $this->createKey($gatewayId, '_queue');
        $queue = [];
        $item = $this->cache->getItem($key);
        if ($item->isHit()) {
            $queue = $item->get();
        }

        $queue[] = new GenericDataContainer($data);
        $item->set($queue);
        $this->cache->save($item);
    }

    /**
     * Fetch cached data from storage.
     *
     * @param string $gatewayId The gateway ID.
     * @param string $hash      The data hash.
     *
     * @return DataContainer|null The data container or null if no data available
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    public function get($gatewayId, $hash)
    {
        $key = $this->createKey($gatewayId, $hash);
        if (!$this->cache->hasItem($key)) {
            return null;
        }
        $item = $this->cache->getItem($key);

        return $item->get();
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
        $key = $this->createKey($gatewayId, $hash);
        $item = $this->cache->getItem($key);
        $item->set($container);
        $this->cache->save($item);
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
        return $gatewayId . '|' . $hash;
    }
}
