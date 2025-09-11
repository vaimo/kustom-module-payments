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
use Klarna\Kp\Model\Initialization\Payload\RequestFetcher;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @api
 */
class GetPayLoad extends CsrfAbstract implements HttpPostActionInterface, RequestHandlerInterface
{
    use RequestTrait;

    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var RequestFetcher
     */
    private RequestFetcher $payload;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param RequestInterface $request
     * @param Result $result
     * @param RequestFetcher $payload
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        Result           $result,
        RequestFetcher   $payload,
        LoggerInterface  $logger
    ) {
        $this->request = $request;
        $this->result = $result;
        $this->payload = $payload;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $result = [];

        try {
            $rawParameter = $this->request->getParams();
            $rawParameter['additional_input'] = json_decode($rawParameter['additional_input'], true);

            $result = $this->payload->getRequest($rawParameter);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }

        return $this->result->getJsonResult(200, $result);
    }
}
