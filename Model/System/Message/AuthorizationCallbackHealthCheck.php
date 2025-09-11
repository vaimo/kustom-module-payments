<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\System\Message;

use DivisionByZeroError;
use Klarna\Logger\Model\LogRepository;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\Phrase;

/**
 * @internal
 */
class AuthorizationCallbackHealthCheck implements MessageInterface
{
    /**
     * @var int
     */
    private int $lookBackDays = 10;

    /**
     * Message identity
     */
    private const MESSAGE_IDENTITY = 'klarna_authorization_callback_health_check';

    /**
     * @var LogRepository
     */
    private LogRepository $logRepository;

    /**
     * @param LogRepository $logRepository
     * @codeCoverageIgnore
     */
    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * Retrieve unique system message identity
     *
     * @return string
     */
    public function getIdentity(): string
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether the system message should be shown
     *
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function isDisplayed(): bool
    {
        return $this->isMoreThanSpecifiedPercentStatus403InLastXDays(30, $this->lookBackDays);
    }

    /**
     * Retrieve system message text
     *
     * @return \Magento\Framework\Phrase
     * @throws \DateMalformedStringException
     */
    public function getText(): Phrase
    {
        $documentationUrl =
            'https://docs.klarna.com/platform-solutions/adobe-commerce/adobe-commerce-244-and-above/' .
            'kp-overview/#authorization-callback';
        return __(
            "
            \"%1\" Klarna orders have failed to complete in the last %4 days.
            <br />
            Please make sure your shop's authorization callback URL is publicly available
            %2based on this documentation%3.
            <br />
            Note: It may take up to %4 days after a fix for this message to dismiss.
            ",
            $this->totalFailedOrdersAttempts($this->getXDaysAgo($this->lookBackDays)),
            sprintf('<a href="%s" target="_blank">', $documentationUrl),
            '</a>',
            $this->lookBackDays
        );
    }

    /**
     * Retrieve system message severity
     * Possible default system message types:
     * - MessageInterface::SEVERITY_CRITICAL
     * - MessageInterface::SEVERITY_MAJOR
     * - MessageInterface::SEVERITY_MINOR
     * - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity(): int
    {
        return self::SEVERITY_CRITICAL;
    }

    /**
     * Check for percentage of 403 status code the shop received on last X amount of days
     *
     * @param int $percentage
     * @param int $days
     * @return bool
     * @throws \DateMalformedStringException
     */
    private function isMoreThanSpecifiedPercentStatus403InLastXDays(int $percentage, int $days): bool
    {
        try {
            $totalRecords = $this->totalCreateOrdersAttempts($this->getXDaysAgo($days));
            $status403Records = $this->totalFailedOrdersAttempts($this->getXDaysAgo($days));

            $totalFailedOrdersPercentage = (int) (($status403Records / $totalRecords) * 100);
        } catch (DivisionByZeroError $exception) {
            return false;
        }

        // Check if more than $percentage of records have status 403
        return $totalRecords > 0 && $totalFailedOrdersPercentage > $percentage;
    }

    /**
     * Total number of failed create order attempts
     *
     * @param string $xDaysAgo
     * @return int
     */
    private function totalFailedOrdersAttempts(string $xDaysAgo): int
    {
        return $this->logRepository->getTotalFailedOrdersAttempts($xDaysAgo);
    }

    /**
     * Total number of create order attempts
     *
     * @param string $xDaysAgo
     * @return int
     */
    private function totalCreateOrdersAttempts(string $xDaysAgo): int
    {
        return $this->logRepository->getTotalCreateOrdersAttempts($xDaysAgo);
    }

    /**
     * The full date of X days ago
     *
     * @param int $days
     * @return string
     * @throws \DateMalformedStringException
     */
    private function getXDaysAgo(int $days): string
    {
        $currentDate = new \DateTime();

        return $currentDate->modify("-{$days} days")->format('Y-m-d H:i:s');
    }
}
