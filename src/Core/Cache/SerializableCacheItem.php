<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Core\Cache;

/**
 * Serializable cache data item.
 *
 * @api
 * @since 1.0
 */
class SerializableCacheItem implements CacheItem, \Serializable
{
    /**
     * Item data.
     *
     * @var array
     */
    private $data;

    /**
     * Create new item.
     *
     * @param string $data The data to be cached.
     *
     * @since 1.0
     */
    public function __construct($data)
    {
        $this->data = [
            'data' => (string) $data,
            'meta' => []
        ];
    }

    /**
     * Retrieves the value of the item from the cache.
     *
     * @return string The value corresponding to this cache item.
     *
     * @since 1.0
     */
    public function get()
    {
        return $this->data['data'];
    }

    /**
     * Sets the value represented by this cache item.
     *
     * @param string $value The serialized value to be stored.
     *
     * @return $this The invoked object.
     *
     * @since 1.0
     */
    public function set($value)
    {
        $this->data['data'] = (string) $value;
        return $this;
    }

    /**
     * Retrieves the meta data with a given key.
     *
     * @param string $key The key.
     *
     * @return string|null The value corresponding to this key.
     *
     * @since 1.0
     */
    public function getMeta($key)
    {
        if (array_key_exists($key, $this->data['meta'])) {
            return $this->data['meta'][$key];
        }
        return null;
    }

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
    public function setMeta($key, $value)
    {
        $this->data['meta'][$key] = (string) $value;
        return $this;
    }

    /**
     * String representation of object
     *
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Constructs the object
     *
     * @param string $serialized The string representation of the object.
     */
    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
    }
}
