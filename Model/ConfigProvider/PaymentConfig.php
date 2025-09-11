<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\ConfigProvider;

use Klarna\AdminSettings\Model\Configurations\Kp as ConfigurationKp;
use Klarna\Kp\Model\Initialization\Action;
use Klarna\Kp\Model\PaymentMethods\PaymentMethodProvider;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote;

/**
 * @internal
 */
class PaymentConfig
{
    /**
     * @var ValidationHandler
     */
    private ValidationHandler $validationHandler;
    /**
     * @var ConfigurationKp
     */
    private ConfigurationKp $paymentConfiguration;
    /**
     * @var Action
     */
    private Action $action;
    /**
     * @var PaymentMethodProvider
     */
    private PaymentMethodProvider $paymentMethodProvider;

    /**
     * @param ValidationHandler $validationHandler
     * @param ConfigurationKp $paymentConfiguration
     * @param Action $action
     * @param PaymentMethodProvider $paymentMethodProvider
     * @codeCoverageIgnore
     */
    public function __construct(
        ValidationHandler $validationHandler,
        ConfigurationKp $paymentConfiguration,
        Action $action,
        PaymentMethodProvider $paymentMethodProvider
    ) {
        $this->validationHandler = $validationHandler;
        $this->paymentConfiguration = $paymentConfiguration;
        $this->action = $action;
        $this->paymentMethodProvider = $paymentMethodProvider;
    }

    /**
     * Get base payments configurations
     *
     * @param Store $store
     * @return array[][]
     */
    public function getPaymentConfig(Store $store): array
    {
        return [
            'payment' => [
                'klarna_kp' => [
                    'client_token'        => null,
                    'message'             => null,
                    'success'             => 0,
                    'enabled'             => $this->validationHandler->isKpEnabled($store),
                    'b2b_enabled'         => $this->paymentConfiguration->isB2bEnabled($store),
                    'available_methods'   => [
                        'type'      => 'klarna_kp',
                        'component' => 'Klarna_Kp/js/view/payments/kp'
                    ]
                ]
            ]
        ];
    }

    /**
     * Update configuration with quote data and return updated configuration
     *
     * @param Quote $quote
     * @param array $paymentConfig
     * @return string[][]
     */
    public function updateWithQuoteData(Quote $quote, array $paymentConfig): array
    {
        $klarnaQuote = $this->action->sendRequest($quote);
        if (!$klarnaQuote->isActive()) {
            return $paymentConfig;
        }
        $paymentConfig['payment']['klarna_kp']['client_token'] = $klarnaQuote->getClientToken();
        $paymentConfig['payment']['klarna_kp']['authorization_token'] = $klarnaQuote->getAuthorizationToken();
        $paymentConfig['payment']['klarna_kp']['success'] = 1;
        $paymentConfig['payment']['klarna_kp']['is_kec_session'] = $klarnaQuote->isKecSession();
        $methods = $klarnaQuote->getPaymentMethodInfo();

        return $this->paymentMethodProvider->getAvailablePaymentMethods($methods, $paymentConfig);
    }
}
