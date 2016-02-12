<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Storage;

/**
 * Storage item.
 *
 * @api
 * @since 1.0
 */
final class Item
{
    /**
     * Data to be stored.
     *
     * @var mixed
     */
    private $data;

    /**
     * Create new storage item.
     *
     * @param mixed $data Data to be queued.
     *
     * @since 1.0
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Return data
     *
     * @return mixed
     *
     * @since 1.0
     */
    public function getData()
    {
        return $this->data;
    }
}
