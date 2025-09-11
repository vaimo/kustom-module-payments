<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api;

use Klarna\Kp\Api\Data\AddressInterface;
use Klarna\Kp\Api\Data\AttachmentInterface;
use Klarna\Kp\Api\Data\OrderlineInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\UrlsInterface;
use Klarna\Kp\Model\Api\Request\Customer;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @internal
 */
class Request implements RequestInterface
{
    /**
     * @var ?string
     */
    private ?string $merchant_reference1 = '';

    /**
     * @var ?string
     */
    private ?string $merchant_reference2 = '';

    /**
     * @var ?Customer
     */
    private ?Customer $customer = null;

    /**
     * @var ?UrlsInterface
     */
    private ?UrlsInterface $urls = null;

    /**
     * @var ?string
     */
    private ?string $purchase_country = null;

    /**
     * @var ?string
     */
    private ?string $purchase_currency = null;

    /**
     * @var ?string
     */
    private ?string $locale = null;

    /**
     * @var ?AddressInterface
     */
    private ?AddressInterface $billing_address = null;

    /**
     * @var ?AddressInterface
     */
    private ?AddressInterface $shipping_address = null;

    /**
     * @var ?int
     */
    private ?int $order_tax_amount = null;

    /**
     * @var ?OrderlineInterface[]
     */
    private ?array $order_lines = null;

    /**
     * @var ?AttachmentInterface
     */
    private ?AttachmentInterface $attachment = null;

    /**
     * @var ?int
     */
    private ?int $order_amount = 0;

    /**
     * @var ?string
     */
    private ?string $design = '';

    /**
     * @var ?array
     */
    private ?array $custom_payment_methods = [];

    /**
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = property_exists($this, $key) ? $value : null;
        }
    }

    /**
     * Used for storing merchant's internal order number or other reference (max 255 characters).
     *
     * @param string $reference
     */
    public function setMerchantReference1(string $reference): void
    {
        \file_put_contents('/tmp/gabor.log', "\n".__METHOD__, FILE_APPEND);
        \file_put_contents('/tmp/gabor.log', "\n"."  \$reference = ".var_export($reference, true), FILE_APPEND);

        $this->merchant_reference1 = $reference;
    }

    /**
     * Used for storing merchant's internal order number or other reference (max 255 characters).
     *
     * @param string $reference
     */
    public function setMerchantReference2(string $reference): void
    {
        $this->merchant_reference2 = $reference;
    }

    /**
     * Setting the merchant urls
     *
     * @param UrlsInterface $urls
     */
    public function setMerchantUrls(UrlsInterface $urls): void
    {
        $this->urls = $urls;
    }

    /**
     * ISO 3166 alpha-2 => Purchase country
     *
     * @param string $purchaseCountry
     */
    public function setPurchaseCountry(string $purchaseCountry): void
    {
        $this->purchase_country = $purchaseCountry;
    }

    /**
     * ISO 4217 => Purchase currency.
     *
     * @param string $purchaseCurrency
     */
    public function setPurchaseCurrency(string $purchaseCurrency): void
    {
        $this->purchase_currency = $purchaseCurrency;
    }

    /**
     * Getting back the purchase currency
     *
     * @return string
     */
    public function getPurchaseCurrency(): string
    {
        return $this->purchase_currency;
    }

    /**
     * RFC 1766 => Customer's locale.
     *
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * The billing address.
     *
     * @param AddressInterface $billingAddress
     */
    public function setBillingAddress(AddressInterface $billingAddress): void
    {
        $this->billing_address = $billingAddress;
    }

    /**
     * The shipping address.
     *
     * @param AddressInterface $shippingAddress
     */
    public function setShippingAddress(AddressInterface $shippingAddress): void
    {
        $this->shipping_address = $shippingAddress;
    }

    /**
     * The total tax amount of the order. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $orderTaxAmount
     */
    public function setOrderTaxAmount(int $orderTaxAmount): void
    {
        $this->order_tax_amount = $orderTaxAmount;
    }

    /**
     * The applicable order lines.
     *
     * @param array|OrderlineInterface[] $orderlines
     */
    public function setOrderLines(array $orderlines): void
    {
        $this->order_lines = $orderlines;
    }

    /**
     * Add an orderLine to the request.
     *
     * @param OrderlineInterface $orderLine
     */
    public function addOrderLine(OrderlineInterface $orderLine): void
    {
        $this->order_lines[] = $orderLine;
    }

    /**
     * Container for optional merchant specific data.
     *
     * @param AttachmentInterface $attachment
     */
    public function setAttachment(AttachmentInterface $attachment): void
    {
        $this->attachment = $attachment;
    }

    /**
     * Total amount of the order, including tax and any discounts. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $orderAmount
     */
    public function setOrderAmount(int $orderAmount): void
    {
        $this->order_amount = $orderAmount;
    }

    /**
     * Used to load a specific design for the credit form
     *
     * @param string $design
     */
    public function setDesign(string $design): void
    {
        $this->design = $design;
    }

    /**
     * An optional list of merchant triggered payment methods
     *
     * @param array $paymentMethods
     */
    public function setCustomPaymentMethods(array $paymentMethods): void
    {
        $this->custom_payment_methods = $paymentMethods;
    }

    /**
     * Add a merchant triggered payment method to request
     *
     * @param string $paymentMethod
     */
    public function addCustomPaymentMethods(string $paymentMethod): void
    {
        $this->custom_payment_methods[] = $paymentMethod;
    }

    /**
     * Getting back the customer name
     *
     * @return string
     */
    public function getCustomerType(): string
    {
        return $this->customer->getType();
    }

    /**
     * Generate array object needed for API call
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function toArray(): array
    {
        \file_put_contents('/tmp/gabor.log', "\n".__METHOD__, FILE_APPEND);

        $request = [
            'purchase_country'       => $this->purchase_country,
            'purchase_currency'      => $this->purchase_currency,
            'locale'                 => $this->locale,
            'order_amount'           => $this->order_amount,
            'order_tax_amount'       => $this->order_tax_amount,
            'order_lines'            => null,
            'billing_address'        => null,
            'shipping_address'       => null,
            'customer'               => null,
            'merchant_urls'          => null,
            'merchant_reference1'    => $this->merchant_reference1,
            'merchant_reference2'    => $this->merchant_reference2,
            'design'                 => $this->design,
            'custom_payment_methods' => $this->custom_payment_methods,
            'attachment'             => null
        ];
        if (null !== $this->billing_address) {
            $request['billing_address'] = $this->billing_address->toArray();
        }
        if (null !== $this->shipping_address) {
            $request['shipping_address'] = $this->shipping_address->toArray();
        }
        if (null !== $this->customer) {
            $request['customer'] = $this->customer->toArray();
        }
        if (null !== $this->urls) {
            $request['merchant_urls'] = $this->urls->toArray();
        }
        if (null !== $this->attachment) {
            $request['attachment'] = $this->attachment->toArray();
        }
        if (null !== $this->order_lines) {
            $request['order_lines'] = [];
            foreach ($this->order_lines as $line) {
                $request['order_lines'][] = $line->toArray();
            }
        }
        return array_filter($request, function ($value) {
            if ($value === null) {
                return false;
            }
            if (is_array($value) && count($value) === 0) {
                return false;
            }
            return true;
        });
    }
}
