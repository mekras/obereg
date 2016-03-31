<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Policy\Inbound;

use Mekras\Obereg\Storage\DataContainer;

/**
 * Inbound transfers processing policy
 *
 * @api
 * @since 1.0
 */
interface InboundPolicy
{
    /**
     * Default value to return if inbound transfer failed and no cached value available.
     *
     * @return mixed
     *
     * @since 1.0
     */
    public function getDefault();

    /**
     * Return true if stored data is still actual.
     *
     * @param DataContainer $dataContainer
     *
     * @return bool
     *
     * @since 1.0
     */
    public function isActual(DataContainer $dataContainer);
}
