<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Storage;

/**
 * Generic data container.
 *
 * @api
 * @since 1.0
 */
class GenericDataContainer implements DataContainer
{
    /**
     * Data.
     *
     * @var string
     */
    private $data;

    /**
     * When was container created.
     *
     * @var int
     */
    private $created;

    /**
     * When was container accessed.
     *
     * @var int
     */
    private $accessed;

    /**
     * Create container for a given data.
     *
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = (string) $data;
        $this->created = time();
        $this->accessed = $this->created;
    }

    /**
     * Returns data as a string
     *
     * @return string
     *
     * @since 1.0
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return timestamp when this container was created.
     *
     * @return int
     *
     * @since 1.0
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Return timestamp when this container was last accessed.
     *
     * @return int
     *
     * @since 1.0
     */
    public function getLastAccessed()
    {
        return $this->accessed;
    }
}
