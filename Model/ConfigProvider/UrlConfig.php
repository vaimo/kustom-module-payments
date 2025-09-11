<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\ConfigProvider;

use Magento\Framework\UrlInterface;

/**
 * @internal
 */
class UrlConfig
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     * @codeCoverageIgnore
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get urls configurations
     *
     * @return string[]
     */
    public function getUrls(): array
    {
        return [
            'reload_checkout_config_url' => $this->urlBuilder->getUrl('checkout/klarna/checkoutConfig'),
            'redirect_url' => $this->urlBuilder->getUrl('checkout/klarna/cookie'),
            'get_quote_status_url' => $this->urlBuilder->getUrl('checkout/klarna/quoteStatus'),
            'authorization_token_update_url' => $this->urlBuilder->getUrl('checkout/klarna/authorizationTokenUpdate'),
            'update_quote_email_url' => $this->urlBuilder->getUrl('checkout/klarna/updateQuoteEmail'),
            'current_session_data_url' => $this->urlBuilder->getUrl('checkout/klarna/currentSessionData'),
        ];
    }
}
