<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class KpConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var PaymentConfig
     */
    private PaymentConfig $paymentConfigProvider;
    /**
     * @var UrlConfig
     */
    private UrlConfig $urlConfigProvider;
    /**
     * @var ValidationHandler
     */
    private ValidationHandler $validationHandler;

    /**
     * @param Session $session
     * @param PaymentConfig $paymentConfigProvider
     * @param UrlConfig $urlConfigProvider
     * @param ValidationHandler $validationHandler
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $session,
        PaymentConfig $paymentConfigProvider,
        UrlConfig $urlConfigProvider,
        ValidationHandler $validationHandler
    ) {
        $this->session = $session;
        $this->paymentConfigProvider = $paymentConfigProvider;
        $this->urlConfigProvider = $urlConfigProvider;
        $this->validationHandler = $validationHandler;
    }

    /**
     * Return payment config for frontend JS to use
     *
     * @return array[][]
     */
    public function getConfig(): array
    {
        $store = $this->session->getQuote()->getStore();
        $paymentConfig = $this->paymentConfigProvider->getPaymentConfig($store);
        $paymentConfig['payment']['klarna_kp'] = array_replace_recursive(
            $paymentConfig['payment']['klarna_kp'],
            $this->urlConfigProvider->getUrls()
        );

        if (!$this->validationHandler->validateApi($this->session->getQuote())) {
            $paymentConfig['payment']['klarna_kp']['message'] = $this->validationHandler->getValidationMessage();
            return $paymentConfig;
        }
        return $this->paymentConfigProvider->updateWithQuoteData($this->session->getQuote(), $paymentConfig);
    }
}
