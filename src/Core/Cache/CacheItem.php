<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Core\Cache;

/**
 * Interface for data item inside a cache.
 *
 * @api
 * @since 1.0
 */
interface CacheItem
{
    /**
     * Retrieves the value of the item from the cache.
     *
     * @return string The value corresponding to this cache item.
     *
     * @since 1.0
     */
    public function get();

    /**
     * Sets the value represented by this cache item.
     *
     * @param string $value The serialized value to be stored.
     *
     * @return $this The invoked object.
     *
     * @since 1.0
     */
    public function set($value);

    /**
     * Retrieves the meta data with a given key.
     *
     * @param string $key The key.
     *
     * @return string|null The value corresponding to this key.
     *
     * @since 1.0
     */
    public function getMeta($key);

    /**
     * Sets the meta data for a given key.
     *
     * @param string $key   The key.
     * @param string $value The data for the $key.
     *
     * @return $this The invoked object.
     *
     * @since 1.0
     */
    public function setMeta($key, $value);
}
