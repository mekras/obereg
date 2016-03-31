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
     * Return true if data can be resent later.
     *
     * @return bool
     */
    public function isResendAllowed();

    /**
     * Return true if data can be resent now.
     *
     * @param DataContainer $dataContainer
     *
     * @return bool
     */
    public function isReadyToResend(DataContainer $dataContainer);
}
