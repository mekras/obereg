<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Storage;

/**
 * Data container.
 *
 * @api
 * @since 1.0
 */
interface DataContainer
{
    /**
     * Returns data as a string
     *
     * @return string
     *
     * @since 1.0
     */
    public function getData();

    /**
     * Return timestamp when this container was created.
     *
     * @return int
     *
     * @since 1.0
     */
    public function getCreated();

    /**
     * Return timestamp when this container was last accessed.
     *
     * @return int
     *
     * @since 1.0
     */
    public function getLastAccessed();
}
