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
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * @api
 */
class UpdateQuoteEmail implements HttpPostActionInterface, RequestHandlerInterface
{
    use RequestTrait;
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * @param RequestInterface $request
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param Result $result
     * @param CartRepositoryInterface $cartRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface        $request,
        CheckoutSession         $checkoutSession,
        CustomerSession         $customerSession,
        Result                  $result,
        CartRepositoryInterface $cartRepository
    ) {
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->result = $result;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Updating the quote email to the correct one
     *
     * @return Json
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $rawParameter = json_decode($this->request->getContent(), true);
            if (!isset($rawParameter['email'])) {
                return $this->result->getJsonResult(204);
            }

            $email = $rawParameter['email'];

            $quote = $this->checkoutSession->getQuote();
            $quote->getBillingAddress()->setEmail($email);
            $this->cartRepository->save($quote);
        }

        return $this->result->getJsonResult(204);
    }
}
