<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * @api
 */
interface RequestInterface extends ApiObjectInterface
{
    /**
     * Used for storing merchant's internal order number or other reference (max 255 characters).
     *
     * @param string $reference
     */
    public function setMerchantReference1(string $reference): void;

    /**
     * Used for storing merchant's internal order number or other reference (max 255 characters).
     *
     * @param string $reference
     */
    public function setMerchantReference2(string $reference): void;

    /**
     * Setting the merchant urls
     *
     * @param UrlsInterface $urls
     */
    public function setMerchantUrls(UrlsInterface $urls): void;

    /**
     * ISO 3166 alpha-2: Purchase country
     *
     * @param string $purchaseCountry
     */
    public function setPurchaseCountry(string $purchaseCountry): void;

    /**
     * ISO 4217: Purchase currency.
     *
     * @param string $purchaseCurrency
     */
    public function setPurchaseCurrency(string $purchaseCurrency): void;

    /**
     * Getting back the purchase currency
     *
     * @return string
     */
    public function getPurchaseCurrency(): string;

    /**
     * RFC 1766: Customer's locale.
     *
     * @param string $locale
     */
    public function setLocale(string $locale): void;

    /**
     * The billing address.
     *
     * @param AddressInterface $billingAddress
     */
    public function setBillingAddress(AddressInterface $billingAddress): void;

    /**
     * The shipping address.
     *
     * @param AddressInterface $shippingAddress
     */
    public function setShippingAddress(AddressInterface $shippingAddress): void;

    /**
     * The total tax amount of the order. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $orderTaxAmount
     */
    public function setOrderTaxAmount(int $orderTaxAmount): void;

    /**
     * The applicable order lines.
     *
     * @param OrderlineInterface[] $orderlines
     */
    public function setOrderLines(array $orderlines): void;

    /**
     * Add an orderLine to the request.
     *
     * @param OrderlineInterface $orderLine
     */
    public function addOrderLine(OrderlineInterface $orderLine): void;

    /**
     * Container for optional merchant specific data.
     *
     * @param AttachmentInterface $attachment
     */
    public function setAttachment(AttachmentInterface $attachment): void;

    /**
     * Total amount of the order, including tax and any discounts. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $orderAmount
     */
    public function setOrderAmount(int $orderAmount): void;

    /**
     * Used to load a specific design for the credit form
     *
     * @param string $design
     */
    public function setDesign(string $design): void;

    /**
     * An optional list of merchant triggered payment methods
     *
     * @param array $paymentMethods
     */
    public function setCustomPaymentMethods(array $paymentMethods): void;

    /**
     * Add a merchant triggered payment method to request
     *
     * @param string $paymentMethod
     */
    public function addCustomPaymentMethods(string $paymentMethod): void;
}
