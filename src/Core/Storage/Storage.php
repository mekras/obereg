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
 * @since x.x
 */
interface Storage
{
    /**
     * Store given data
     *
     * @param string $groupId Queue or cache ID.
     * @param mixed  $data    Data to be stored.
     *
     * @return void
     *
     * @since x.x
     */
    public function store($groupId, $data);

    /**
     * Fetch next data item.
     *
     * @param string $groupId Queue or cache ID.
     *
     * @return Item[] The items of a specified group.
     *
     * @since x.x
     */
    public function fetch($groupId);
}
