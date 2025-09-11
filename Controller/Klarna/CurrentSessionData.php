<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Kp\Controller\Klarna;

use Klarna\Base\Api\RequestHandlerInterface;
use Klarna\Base\Controller\RequestTrait;
use Klarna\Base\Model\Responder\Result;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Api\Builder\Request as RequestBuilder;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

/**
 * @api
 */
class CurrentSessionData implements HttpGetActionInterface, RequestHandlerInterface
{
    use RequestTrait;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdFactory;
    /**
     * @var RequestBuilder
     */
    private RequestBuilder $requestBuilder;

    /**
     * @param RequestInterface $request
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdFactory
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @param RequestBuilder $requestBuilder
     * @param Result $result
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface                $request,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdFactory,
        QuoteRepositoryInterface        $klarnaQuoteRepository,
        CartRepositoryInterface         $magentoQuoteRepository,
        RequestBuilder                  $requestBuilder,
        Result                          $result
    ) {
        $this->request = $request;
        $this->maskedQuoteIdFactory = $maskedQuoteIdFactory;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
        $this->requestBuilder = $requestBuilder;
        $this->result = $result;
    }

    /**
     * Redirecting the customer to a url to set the cookie.
     */
    public function execute()
    {
        try {
            $maskedQuoteIdParam = $this->request->getParam('maskedQuoteId');
            $magentoQuoteIdParam = $this->request->getParam('quoteId');

            $magentoQuoteId = $magentoQuoteIdParam ?? $this->maskedQuoteIdFactory->execute($maskedQuoteIdParam);
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuoteId((string)$magentoQuoteId);
            $magentoQuote = $this->magentoQuoteRepository->get($magentoQuoteId);

            $placeOrderRequest = $this
                ->requestBuilder
                ->generateFullRequest($magentoQuote, $klarnaQuote->getAuthTokenCallbackToken());

            return $this->result->getJsonResult(
                200,
                [
                    'message' => __('Klarna payments session retrieved successfully.'),
                    'data' => $placeOrderRequest->toArray()
                ]
            );
        } catch (\Exception $exception) {
            return $this->result->getJsonResult(400, [
                'error' => $exception->getMessage(),
                'message' => __('Unable to retrieve Klarna payments session.'),
            ]);
        }
    }
}
