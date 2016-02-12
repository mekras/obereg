<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Queue;

use Mekras\Obereg\Gateway\Gateway;
use Mekras\Obereg\Storage\Item;
use Mekras\Obereg\Storage\Storage;

/**
 * Outbound transfers queue.
 *
 * @api
 * @since x.x
 */
class Queue
{
    /**
     * Queue permanent ID
     *
     * @var string
     */
    private $id;

    /**
     * Data storage
     *
     * @var Storage
     */
    private $storage;

    /**
     * Create queue.
     *
     * @param Gateway $gateway Queue gateway
     * @param Storage $storage Data storage
     */
    public function __construct(Gateway $gateway, Storage $storage)
    {
        $this->id = $gateway->getId() . '-queue';
        $this->storage = $storage;
    }

    /**
     * Push data to queue.
     *
     * @param string $data The data as a string to store.
     *
     * @return void
     *
     * @since x.x
     */
    public function enqueue($data)
    {
        $this->storage->store($this->id, $data);
    }

    /**
     * Fetch next data item.
     *
     * @return Item|null The data item or null if queue is empty.
     *
     * @since x.x
     */
    public function dequeue()
    {
        $items = $this->storage->fetch($this->id);
        return count($items) ? $items[0] : null;
    }
}
