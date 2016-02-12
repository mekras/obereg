<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Core;

use Mekras\Obereg\Core\Exception\UnserializeException;

/**
 * Generic data serializer.
 *
 * Uses standard {@link http://php.net/serialize serialize()} and
 * {@link http://php.net/unserialize unserialize()} functions.
 *
 * @api
 * @since 1.0
 */
class DataSerializer
{
    /**
     * Serialize data to binary string.
     *
     * @param mixed $data
     *
     * @return string
     *
     * @since 1.0
     */
    public function serialize($data)
    {
        return serialize($data);
    }

    /**
     * Unserialize data from binary string.
     *
     * @param string $string
     *
     * @return mixed Unserialized data.
     *
     * @throws UnserializeException If $string can not be unserialized.
     *
     * @since 1.0
     */
    public function unserialize($string)
    {
        return unserialize($string);
    }
}
