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
     * @param int $delay Delay between resend trials in seconds.
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0
     */
    public function __construct($delay = 0)
    {
        $this->delay = (int) $delay;
        if ($this->delay < 0) {
            throw new \InvalidArgumentException('Delay can not be negative');
        }
    }

    /**
     * Return true if the given data can be resented now.
     *
     * @param DataContainer $dataContainer
     *
     * @return bool
     */
    public function isResendAllowed(DataContainer $dataContainer)
    {
        if (0 === $this->delay) {
            return true;
        }
        return time() - $dataContainer->getLastAccessed() > $this->delay;
    }
}
