<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Placement;

use Klarna\Base\Exception as KlarnaBaseException;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Model\Api\Builder\Request;
use Klarna\Kp\Model\Api\Container;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class Api
{
    /**
     * @var CreditApiInterface
     */
    private CreditApiInterface $api;
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var Container
     */
    private Container $container;

    /**
     * @param CreditApiInterface $api
     * @param Request $request
     * @param Container $container
     * @codeCoverageIgnore
     */
    public function __construct(CreditApiInterface $api, Request $request, Container $container)
    {
        $this->api = $api;
        $this->request = $request;
        $this->container = $container;
    }

    /**
     * Placing the order through the API
     *
     * @param QuoteInterface $klarnaQuote
     * @param CartInterface $magentoQuote
     * @param string $orderIncrementId
     * @param string $authorizationToken
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaBaseException
     */
    public function placeKlarnaOrder(
        QuoteInterface $klarnaQuote,
        CartInterface $magentoQuote,
        string $orderIncrementId,
        string $authorizationToken
    ): ResponseInterface {
        $placeOrderRequest = $this->request->generatePlaceOrderRequest(
            $magentoQuote,
            $klarnaQuote->getAuthTokenCallbackToken()
        );

        $this->api->setKlarnaQuote($klarnaQuote);
        $this->container->setAuthorizationToken($authorizationToken);

        if (!$klarnaQuote->isKecSession()) {
            $this->container->setSessionId($klarnaQuote->getSessionId());
        }

        $this->container->setIncrementId($orderIncrementId);
        $this->container->setRequest($placeOrderRequest);
        $this->container->setCurrency($magentoQuote->getBaseCurrencyCode());
        $placeKlarnaOrderResponse = $this->api->placeOrder($this->container);

        if (!$placeKlarnaOrderResponse->isSuccessfull()) {
            throw new KlarnaBaseException(__('Unable to authorize payment/place the order.'));
        }

        if ($placeKlarnaOrderResponse->getRedirectUrl()) {
            $klarnaQuote->setRedirectUrl($placeKlarnaOrderResponse->getRedirectUrl());
        }
        return $placeKlarnaOrderResponse;
    }
}
