<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\PaymentMethods;

use Klarna\Kp\Model\PaymentMethods\AdditionalInformationUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Kp\Model\Quote as KlarnaQuote;
use Magento\Quote\Model\Quote\Payment;
use Magento\Quote\Model\Quote as MagentoQuote;
use Magento\Framework\DataObject;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @coversDefaultClass \Klarna\Kp\Model\PaymentMethods\AdditionalInformationUpdater
 */
class AdditionalInformationUpdaterTest extends TestCase
{
    /**
     * @var AdditionalInformationUpdater
     */
    private AdditionalInformationUpdater $additionalInformationUpdater;
    /**
     * @var Quote|MockObject
     */
    private KlarnaQuote $klarnaQuote;
    /**
     * @var Payment|MockObject
     */
    private Payment $payment;
    /**
     * @var DataObject|MockObject
     */
    private DataObject $additionalInformation;

    public function testUpdateByInputNoActiveQuoteWasFound(): void
    {
        $this->dependencyMocks['klarnaQuoteRepository']->expects(static::never())
            ->method('save');

        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuote')
            ->willThrowException(new NoSuchEntityException(__('any message')));

        $this->additionalInformationUpdater->updateByInput($this->additionalInformation, $this->payment);
    }

    protected function setUp(): void
    {
        $this->additionalInformationUpdater = parent::setUpMocks(AdditionalInformationUpdater::class);

        $this->klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);

        $magentoQuote = $this->mockFactory->create(MagentoQuote::class);
        $methodInstance = $this->mockFactory->create(Kp::class);
        $this->payment = $this->mockFactory->create(Payment::class);
        $this->payment->method('getQuote')
            ->willReturn($magentoQuote);
        $this->payment->method('getMethodInstance')
            ->willReturn($methodInstance);

        $this->additionalInformation = $this->mockFactory->create(DataObject::class);
    }
}
