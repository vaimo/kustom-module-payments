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
use Klarna\Base\Controller\CsrfAbstract;
use Klarna\Base\Controller\RequestTrait;
use Klarna\Base\Model\Responder\Result;
use Klarna\Kp\Api\AuthorizationCallbackStatusInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Logger\Authorize as AuthorizeLogger;
use Klarna\Kp\Model\Placement\AuthorizationCallback\RequestValidator;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * @api
 */
class Authorize extends CsrfAbstract implements HttpPostActionInterface, RequestHandlerInterface
{
    use RequestTrait;

    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var AuthorizeLogger
     */
    private AuthorizeLogger $authorizeLogger;
    /**
     * @var CartManagementInterface
     */
    private CartManagementInterface $cartManagement;
    /**
     * @var RequestValidator
     */
    private RequestValidator $requestValidator;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;

    /**
     * @param RequestInterface $request
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param Result $result
     * @param LoggerInterface $logger
     * @param AuthorizeLogger $authorizeLogger
     * @param CartManagementInterface $cartManagement
     * @param RequestValidator $requestValidator
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        Result $result,
        LoggerInterface $logger,
        AuthorizeLogger $authorizeLogger,
        CartManagementInterface $cartManagement,
        RequestValidator $requestValidator,
        CartRepositoryInterface $magentoQuoteRepository
    ) {
        $this->request = $request;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->result = $result;
        $this->logger = $logger;
        $this->authorizeLogger = $authorizeLogger;
        $this->cartManagement = $cartManagement;
        $this->requestValidator = $requestValidator;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
    }

    /**
     * Performing the authorization callback action
     *
     * @return Json
     */
    public function execute()
    {
        try {
            $parameters = json_decode($this->request->getContent(), true);

            if ($this->request->getParam('dryRun')) {
                return $this->result->getJsonResult(
                    200,
                    [
                        'message' => 'The ' . $this->request->getRequestUri() . ' is accessible.',
                        'timestamp' => time(),
                        'code' => 200
                    ]
                );
            }

            $this->requestValidator->validateRequestBody();
            $this->authorizeLogger->logRequest($parameters['session_id'], $this->request);

            $klarnaQuote = $this->klarnaQuoteRepository->getBySessionId($parameters['session_id']);

            $this->requestValidator->verifyAuthCallbackToken($klarnaQuote);
            $this->requestValidator->verifyMagentoQuote((string) $klarnaQuote->getQuoteId());

            if ($klarnaQuote->isAuthCallbackAlreadyProcessed()) {
                return $this->result->getJsonResult(400, [
                    'error' => 'Another authorization callback workflow is still in progress.'
                ]);
            }

            $klarnaQuote->setAuthorizationToken($parameters['authorization_token']);
            $klarnaQuote->setAuthCallbackActiveCurrentStatus(AuthorizationCallbackStatusInterface::IN_PROGRESS);
            $this->klarnaQuoteRepository->save($klarnaQuote);

            $magentoQuote = $this->magentoQuoteRepository->get($klarnaQuote->getQuoteId());
            $this->logger->setStore($magentoQuote->getStore());

            $payment = $magentoQuote->getPayment();
            $payment->setAdditionalInformation('authorization_token', $parameters['authorization_token']);

            $this->cartManagement->placeOrder($klarnaQuote->getQuoteId(), $payment);
        } catch (LocalizedException $exception) {
            $this->logger->info('Authorization callback failed. Reason:');
            $this->logger->critical($exception);
            if (isset($klarnaQuote)) {
                $klarnaQuote->setAuthCallbackActiveCurrentStatus(AuthorizationCallbackStatusInterface::FAILED);
                $this->klarnaQuoteRepository->save($klarnaQuote);
            }

            $this->authorizeLogger->logException($parameters['session_id'] ?? '', $this->request, $exception);

            return $this->result->getJsonResult(400, ['error' => $exception->getMessage()]);
        }

        $klarnaQuote->setAuthCallbackActiveCurrentStatus(AuthorizationCallbackStatusInterface::SUCCESSFUL);
        $this->klarnaQuoteRepository->save($klarnaQuote);

        return $this->result->getJsonResult(204, ['message' => 'Order has placed successfully.']);
    }
}
