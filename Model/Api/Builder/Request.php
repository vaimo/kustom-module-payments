<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder;

use Klarna\Base\Exception;
use Klarna\Kp\Model\Api\Builder\Customer\Generator as CustomerGenerator;
use Klarna\Kp\Model\Api\Builder\Nodes\MerchantReferences;
use Klarna\Kp\Model\Api\Builder\Nodes\MerchantUrls;
use Klarna\Kp\Model\Api\Builder\Nodes\Miscellaneous;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderAmount;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderLines;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderTaxAmount;
use Klarna\Kp\Model\Api\Builder\Nodes\PurchaseCountry;
use Klarna\Orderlines\Model\Container\Parameter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Kp\Model\Api\Builder\Nodes\Addresses\Builder as AddressBuilder;
use Klarna\Kp\Api\Data\RequestInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class Request
{
    public const GENERATE_TYPE_CREATE = 'create';
    public const GENERATE_TYPE_UPDATE = 'update';
    public const GENERATE_TYPE_PLACE = 'place';
    private const BASE_REQUIRED_ATTRIBUTES = [
        'purchase_country',
        'purchase_currency',
        'locale',
        'order_amount',
        'order_lines',
        'billing_address'
    ];
    public const GENERATE_REQUIRED_ATTRIBUTES_CREATE_UPDATE = self::BASE_REQUIRED_ATTRIBUTES;
    public const GENERATE_REQUIRED_ATTRIBUTES_PLACE = [
        ...self::BASE_REQUIRED_ATTRIBUTES,
        'merchant_urls'
    ];
    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var MerchantUrls
     */
    private MerchantUrls $merchantUrls;
    /**
     * @var OrderLines
     */
    private OrderLines $orderLines;
    /**
     * @var OrderTaxAmount
     */
    private OrderTaxAmount $orderTaxAmount;
    /**
     * @var OrderAmount
     */
    private OrderAmount $orderAmount;
    /**
     * @var PurchaseCountry
     */
    private PurchaseCountry $purchaseCountry;
    /**
     * @var MerchantReferences
     */
    private MerchantReferences $merchantReferences;
    /**
     * @var CustomerGenerator
     */
    private CustomerGenerator $customerGenerator;
    /**
     * @var Miscellaneous
     */
    private Miscellaneous $miscellaneous;
    /**
     * @var AddressBuilder
     */
    private AddressBuilder $addressBuilder;
    /**
     * @var Parameter
     */
    private Parameter $parameter;

    /**
     * @param Builder $requestBuilder
     * @param MerchantUrls $merchantUrls
     * @param OrderLines $orderLines
     * @param OrderTaxAmount $orderTaxAmount
     * @param OrderAmount $orderAmount
     * @param PurchaseCountry $purchaseCountry
     * @param MerchantReferences $merchantReferences
     * @param CustomerGenerator $customerGenerator
     * @param Miscellaneous $miscellaneous
     * @param AddressBuilder $addressBuilder
     * @param Parameter $parameter
     * @codeCoverageIgnore
     */
    public function __construct(
        Builder $requestBuilder,
        MerchantUrls $merchantUrls,
        OrderLines $orderLines,
        OrderTaxAmount $orderTaxAmount,
        OrderAmount $orderAmount,
        PurchaseCountry $purchaseCountry,
        MerchantReferences $merchantReferences,
        CustomerGenerator $customerGenerator,
        Miscellaneous $miscellaneous,
        AddressBuilder $addressBuilder,
        Parameter $parameter
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->merchantUrls = $merchantUrls;
        $this->orderLines = $orderLines;
        $this->orderTaxAmount = $orderTaxAmount;
        $this->orderAmount = $orderAmount;
        $this->purchaseCountry = $purchaseCountry;
        $this->merchantReferences = $merchantReferences;
        $this->customerGenerator = $customerGenerator;
        $this->miscellaneous = $miscellaneous;
        $this->addressBuilder = $addressBuilder;
        $this->parameter = $parameter;
    }

    /**
     * Generating the create session request
     *
     * @param CartInterface $magentoQuote
     * @param string|null $authCallbackToken
     * @return RequestInterface
     * @throws Exception|\Klarna\Base\Model\Api\Exception|LocalizedException
     */
    public function generateCreateSessionRequest(
        CartInterface $magentoQuote,
        ?string $authCallbackToken
    ): RequestInterface {
        $this->generateCreateUpdateSessionRequest($magentoQuote, $authCallbackToken, self::GENERATE_TYPE_CREATE);
        return $this->requestBuilder->getRequest();
    }

    /**
     * Generating the update session request
     *
     * @param CartInterface $magentoQuote
     * @param string|null $authCallbackToken
     * @return RequestInterface
     * @throws Exception|\Klarna\Base\Model\Api\Exception|LocalizedException
     */
    public function generateUpdateSessionRequest(
        CartInterface $magentoQuote,
        ?string $authCallbackToken
    ): RequestInterface {
        $this->generateCreateUpdateSessionRequest($magentoQuote, $authCallbackToken, self::GENERATE_TYPE_UPDATE);
        return $this->requestBuilder->getRequest();
    }

    /**
     * Generating the request for creating and updating a session
     *
     * @param CartInterface $magentoQuote
     * @param ?string $authCallbackToken
     * @param string $type
     * @throws Exception|\Klarna\Base\Model\Api\Exception|LocalizedException
     */
    private function generateCreateUpdateSessionRequest(
        CartInterface $magentoQuote,
        ?string $authCallbackToken,
        string $type
    ): void {
        $this->resetBuilderAndParameterProcessOrderLines($magentoQuote);

        $this->addToRequest($magentoQuote, $authCallbackToken);

        $requiredAttributes = $this->setRequiredAttributes(
            $magentoQuote,
            self::GENERATE_REQUIRED_ATTRIBUTES_CREATE_UPDATE
        );

        $this->validateRequest($requiredAttributes, $type);
    }

    /**
     * Generating the full request
     *
     * @param CartInterface $magentoQuote
     * @param string $authCallbackToken
     * @return RequestInterface
     * @throws Exception|\Klarna\Base\Model\Api\Exception|LocalizedException
     */
    public function generateFullRequest(CartInterface $magentoQuote, string $authCallbackToken): RequestInterface
    {
        return $this->generatePlaceOrderRequest($magentoQuote, $authCallbackToken);
    }

    /**
     * Creating the place order request
     *
     * @param CartInterface $magentoQuote
     * @param string $authCallbackToken
     * @throws Exception|\Klarna\Base\Model\Api\Exception|LocalizedException
     * @return RequestInterface
     */
    public function generatePlaceOrderRequest(CartInterface $magentoQuote, string $authCallbackToken): RequestInterface
    {
        \file_put_contents('/tmp/gabor.log', "\n".__METHOD__, FILE_APPEND);
        \file_put_contents('/tmp/gabor.log', "\n"."  \$magentoQuote->getReservedOrderId() = ".var_export($magentoQuote->getReservedOrderId(), true), FILE_APPEND);

        $this->resetBuilderAndParameterProcessOrderLines($magentoQuote);

        $this->addToRequest($magentoQuote, $authCallbackToken);
        $this->merchantReferences->addToRequest($this->requestBuilder, $magentoQuote);

        $requiredAttributes = $this->setRequiredAttributes(
            $magentoQuote,
            self::GENERATE_REQUIRED_ATTRIBUTES_PLACE
        );

        $this->validateRequest($requiredAttributes, self::GENERATE_TYPE_PLACE);

        return $this->requestBuilder->getRequest();
    }

    /**
     * Adding the request parameters
     *
     * @param CartInterface $magentoQuote
     * @param string|null $authCallbackToken
     * @return void
     * @throws Exception|LocalizedException
     */
    protected function addToRequest(CartInterface $magentoQuote, ?string $authCallbackToken): void
    {
        $this->addressBuilder->addToRequest($this->requestBuilder, $magentoQuote);
        $this->orderLines->addToRequest($this->requestBuilder, $this->parameter, $magentoQuote);
        $this->orderAmount->addToRequest($this->requestBuilder);
        $this->orderTaxAmount->addToRequest($this->requestBuilder, $this->parameter, $magentoQuote);
        $this->merchantUrls->addToRequest($this->requestBuilder, $authCallbackToken);
        $this->miscellaneous->addToRequest($this->requestBuilder, $magentoQuote);
        $this->purchaseCountry->addToRequest($this->requestBuilder, $magentoQuote);
        $this->requestBuilder->setCustomer($this->customerGenerator->getBasicData($magentoQuote));
    }

    /**
     * Resetting the builder and processing order lines
     *
     * @param CartInterface $magentoQuote
     * @return void
     * @throws LocalizedException
     */
    protected function resetBuilderAndParameterProcessOrderLines(CartInterface $magentoQuote): void
    {
        $this->requestBuilder->reset();

        $this->parameter->resetOrderLines();
        $this->parameter->getOrderLineProcessor()
            ->processByQuote($this->parameter, $magentoQuote);
    }

    /**
     * Validating the generated request
     *
     * @param array $requiredAttributes
     * @param string $type
     * @return void
     * @throws \Klarna\Base\Model\Api\Exception
     */
    protected function validateRequest(array $requiredAttributes, string $type): void
    {
        $validator = $this->requestBuilder->getValidator();
        $validator->isRequiredValueMissing($requiredAttributes, $type);
        $validator->isSumOrderLinesMatchingOrderAmount();
    }

    /**
     * Setting the required attributes for the request, adds shipping_address if quote is not virtual
     *
     * @param CartInterface $magentoQuote
     * @param array $attributesToValidate
     * @return string[]
     */
    protected function setRequiredAttributes(CartInterface $magentoQuote, array $attributesToValidate): array
    {
        $requiredAttributes = $attributesToValidate;
        if (!$magentoQuote->getIsVirtual()) {
            $requiredAttributes[] = 'shipping_address';
        }
        return $requiredAttributes;
    }
}
