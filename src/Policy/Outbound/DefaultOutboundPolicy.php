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
class DefaultOutboundPolicy implements OutboundPolicy
{
    /**
     * Delay between resend trials in seconds.
     *
     * @var int
     */
    private $delay;

    /**
     * Policy constructor.
     *
     * @param int $delay Delay between resend trials in seconds. 0 — now delay, -1 — never resend.
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    public function __construct($delay = 0)
    {
        $this->delay = (int) $delay;
        if ($this->delay < -1) {
            throw new \InvalidArgumentException('Delay can not be less than -1');
        }
    }

    /**
     * Return true if data can be resent later.
     *
     * @return bool
     */
    public function isResendAllowed()
    {
        return -1 !== $this->delay;
    }

    /**
     * Return true if data can be resent now.
     *
     * @param DataContainer $dataContainer
     *
     * @return bool
     */
    public function isReadyToResend(DataContainer $dataContainer)
    {
        if (0 === $this->delay) {
            return true;
        }
        return time() - $dataContainer->getLastAccessed() > $this->delay;
    }
}
