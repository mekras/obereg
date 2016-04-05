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
     * @var int|null
     */
    private $delay;

    /**
     * Policy constructor.
     *
     * @param int|null $delay Delay between resend trials in seconds. null — never resend.
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    public function __construct($delay = null)
    {
        if (null === $delay) {
            $this->delay = null;
        } else {
            $this->delay = (int) $delay;
            if ($this->delay < 1) {
                throw new \InvalidArgumentException('Delay can not be less than 1 second');
            }
        }
    }

    /**
     * Return true if data can be resent later.
     *
     * @return bool
     */
    public function isResendAllowed()
    {
        return null !== $this->delay;
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
        return time() - $dataContainer->getCreated() > $this->delay;
    }
}
