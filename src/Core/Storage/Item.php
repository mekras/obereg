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
 * @since x.x
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
     * @since x.x
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
     * @since x.x
     */
    public function getData()
    {
        return $this->data;
    }
}
