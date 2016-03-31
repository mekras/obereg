<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Policy\Outbound;

use Mekras\Obereg\Storage\DataContainer;

/**
 * Outbound transfers processing policy
 *
 * @api
 * @since 1.0
 */
interface OutboundPolicy
{
    /**
     * Return true if the given data can be resented now.
     *
     * @param DataContainer $dataContainer
     *
     * @return bool
     */
    public function isResendAllowed(DataContainer $dataContainer);
}
