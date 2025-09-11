<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization;

use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Api\Builder\Customer\TypeResolver;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Validator
{
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var TypeResolver
     */
    private TypeResolver $typeResolver;

    /**
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param TypeResolver $typeResolver
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteRepositoryInterface $klarnaQuoteRepository,
        TypeResolver $typeResolver
    ) {
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->typeResolver = $typeResolver;
    }

    /**
     * Returns true if a Klarna session is already running
     *
     * @param QuoteInterface $klarnaQuote
     * @return bool
     */
    public function isKlarnaSessionRunning(QuoteInterface $klarnaQuote): bool
    {
        if ($klarnaQuote->isKecSession()) {
            return true;
        }

        if (!$klarnaQuote->isActive()) {
            return false;
        }

        if (!$klarnaQuote->getSessionId()) {
            $this->klarnaQuoteRepository->markInactive($klarnaQuote);
            return false;
        }

        return true;
    }

    /**
     * Returns true if the customer type changed
     *
     * @param QuoteInterface $klarnaQuote
     * @param CartInterface $quote
     * @return bool
     */
    public function isCustomerTypeChanged(QuoteInterface $klarnaQuote, CartInterface $quote): bool
    {
        $oldCustomerType = $klarnaQuote->getCustomerType();
        $newCustomerType = $this->typeResolver->getData($quote);

        return $oldCustomerType !== $newCustomerType;
    }
}
