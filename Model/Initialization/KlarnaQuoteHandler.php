<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization;

use Klarna\Base\Exception;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Api\Builder\Request as RequestBuilder;
use Klarna\Kp\Model\Api\Container;
use Klarna\Kp\Model\PaymentMethods\PaymentMethodProvider;
use Klarna\Kp\Model\QuoteFactory as KlarnaQuoteFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class KlarnaQuoteHandler
{
    /**
     * @var KlarnaQuoteFactory
     */
    private KlarnaQuoteFactory $klarnaQuoteFactory;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var PaymentMethodProvider
     */
    private PaymentMethodProvider $paymentMethodProvider;
    /**
     * @var RequestBuilder
     */
    private RequestBuilder $requestBuilder;
    /**
     * @var CreditApiInterface
     */
    private CreditApiInterface $api;
    /**
     * @var Random
     */
    private Random $randomGenerator;
    /**
     * @var Container
     */
    private Container $container;
    /**
     * @var DuplicateRequestBlocker
     */
    private DuplicateRequestBlocker $duplicateResponseHandler;

    /**
     * Handle Klarna Quote actions like creation and update
     *
     * @param KlarnaQuoteFactory $klarnaQuoteFactory
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param PaymentMethodProvider $paymentMethodProvider
     * @param RequestBuilder $requestBuilder
     * @param CreditApiInterface $api
     * @param Random $randomGenerator
     * @param Container $container
     * @param DuplicateRequestBlocker $duplicateResponseHandler
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaQuoteFactory $klarnaQuoteFactory,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        PaymentMethodProvider $paymentMethodProvider,
        RequestBuilder $requestBuilder,
        CreditApiInterface $api,
        Random $randomGenerator,
        Container $container,
        DuplicateRequestBlocker $duplicateResponseHandler
    ) {
        $this->klarnaQuoteFactory = $klarnaQuoteFactory;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->requestBuilder = $requestBuilder;
        $this->api = $api;
        $this->randomGenerator = $randomGenerator;
        $this->container = $container;
        $this->duplicateResponseHandler = $duplicateResponseHandler;
    }

    /**
     * Marking the Klarna quote as inactive
     *
     * @param QuoteInterface $klarnaQuote
     * @return void
     */
    public function markInactive(QuoteInterface $klarnaQuote): void
    {
        $this->klarnaQuoteRepository->markInactive($klarnaQuote);
    }

    /**
     * Creating a new Klarna quote
     *
     * @param CartInterface $quote
     * @param bool $disableAuthorizationCallback
     * @return QuoteInterface
     * @throws Exception|KlarnaApiException|LocalizedException
     */
    public function createKlarnaQuote(CartInterface $quote, bool $disableAuthorizationCallback = false): QuoteInterface
    {
        $authCallbackToken = $this->generateAuthorizationCallback($disableAuthorizationCallback);
        $createSessionRequest = $this->requestBuilder->generateCreateSessionRequest($quote, $authCallbackToken);

        $klarnaQuote = $this->klarnaQuoteFactory->create();

        if ($this->duplicateResponseHandler->isDuplicateRequest($createSessionRequest)) {
            return $klarnaQuote;
        }

        $this->container->setRequest($createSessionRequest);
        $this->container->setCurrency($quote->getBaseCurrencyCode());
        $klarnaResponse = $this->api->createSession($this->container);

        $this->duplicateResponseHandler->handleKlarnaResponse($klarnaResponse, $createSessionRequest);

        $klarnaQuote->setRequestData($createSessionRequest);
        $klarnaQuote->setSessionId($klarnaResponse->getSessionId());
        $klarnaQuote->setClientToken($klarnaResponse->getClientToken());
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->setQuoteId($quote->getId());
        $klarnaQuote->setPaymentMethods($this->paymentMethodProvider->extractByApiResponse($klarnaResponse));
        $klarnaQuote->setPaymentMethodInfo($klarnaResponse->getPaymentMethodCategories());
        $klarnaQuote->setAuthTokenCallbackToken((string) $authCallbackToken);
        $klarnaQuote->setCustomerType($createSessionRequest->getCustomerType());

        $this->klarnaQuoteRepository->save($klarnaQuote);
        return $klarnaQuote;
    }

    /**
     * Updating the Klarna quote
     *
     * @param CartInterface $magentoQuote
     * @param QuoteInterface $klarnaQuote
     * @return QuoteInterface
     * @throws Exception|KlarnaApiException|LocalizedException
     */
    public function updateKlarnaQuote(CartInterface $magentoQuote, QuoteInterface $klarnaQuote): QuoteInterface
    {
        $sessionId = $klarnaQuote->getSessionId();
        $updateSessionRequest = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($magentoQuote, $klarnaQuote->getAuthTokenCallbackToken());

        if (!$klarnaQuote->requestDataHaveChanged($updateSessionRequest) ||
            $this->duplicateResponseHandler->isDuplicateRequest($updateSessionRequest)
        ) {
            return $klarnaQuote;
        }

        $this->api->setKlarnaQuote($klarnaQuote);

        $this->container->setRequest($updateSessionRequest);
        $this->container->setSessionId($sessionId);
        $this->container->setCurrency($magentoQuote->getBaseCurrencyCode());
        $klarnaUpdateResponse = $this->api->updateSession($this->container);

        if ($klarnaUpdateResponse->isExpired()) {
            $this->markInactive($klarnaQuote);
            return $klarnaQuote;
        }

        $this->duplicateResponseHandler->handleKlarnaResponse($klarnaUpdateResponse, $updateSessionRequest);

        $klarnaQuote->setRequestData($updateSessionRequest);
        $klarnaQuote->setPaymentMethods($this->paymentMethodProvider->extractByApiResponse($klarnaUpdateResponse));
        $klarnaQuote->setPaymentMethodInfo($klarnaUpdateResponse->getPaymentMethodCategories());
        $klarnaQuote->setCustomerType($updateSessionRequest->getCustomerType());

        $this->klarnaQuoteRepository->save($klarnaQuote);
        return $klarnaQuote;
    }

    /**
     * Generating the authorization callback token
     *
     * @param bool $disableAuthorizationCallback
     * @return string|null
     * @throws LocalizedException
     */
    private function generateAuthorizationCallback(bool $disableAuthorizationCallback): ?string
    {
        if ($disableAuthorizationCallback) {
            return null;
        }

        return $this->randomGenerator->getUniqueHash();
    }
}
