<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Rest\Service;

use Klarna\AdminSettings\Model\Configurations\Api;
use Klarna\Base\Api\ServiceInterface;
use Klarna\Base\Helper\VersionInfo;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Base\Model\Api\Rest\Service;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Logger\Model\Api\Container;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Model\Api\Response;
use Klarna\Kp\Model\Api\ResponseFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Klarna\Base\Exception as KlarnaException;
use Klarna\Kp\Model\Api\Container as ApiContainer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class Payments implements CreditApiInterface
{
    public const API_VERSION = 'v1';

    /**
     * @var ServiceInterface
     */
    private $service;
    /**
     * @var VersionInfo
     */
    private $versionInfo;
    /**
     * @var StoreInterface
     */
    private $store;
    /**
     * @var ResponseFactory
     */
    private $responseFactory;
    /**
     * @var Container
     */
    private $loggerContainer;
    /**
     * @var QuoteInterface
     */
    private $klarnaQuote = null;
    /**
     * @var Api
     */
    private Api $apiConfiguration;

    /**
     * @param StoreManagerInterface $storeManager
     * @param VersionInfo           $versionInfo
     * @param ResponseFactory       $responseFactory
     * @param ServiceInterface      $service
     * @param Container             $loggerContainer
     * @param Api                   $apiConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        VersionInfo $versionInfo,
        ResponseFactory $responseFactory,
        ServiceInterface $service,
        Container $loggerContainer,
        Api $apiConfiguration
    ) {
        $this->service = $service;
        $this->responseFactory = $responseFactory;
        $this->store = $storeManager->getStore();
        $this->versionInfo = $versionInfo;
        $this->loggerContainer = $loggerContainer;
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * Setting the Klarna quote
     *
     * @param QuoteInterface $klarnaQuote
     */
    public function setKlarnaQuote(QuoteInterface $klarnaQuote): void
    {
        $this->klarnaQuote = $klarnaQuote;
    }

    /**
     * Processing the request
     *
     * @param string $url
     * @param string $action
     * @param ApiContainer $apiContainer
     * @param string $method
     * @return Response
     * @throws KlarnaException
     */
    private function processRequest(
        string $url,
        string $action,
        ApiContainer $apiContainer,
        string $method = ServiceInterface::POST,
    ) {
        $this->loggerContainer->setAction($action);
        $body = $this->getBody($apiContainer->getRequest());
        $this->connect($apiContainer->getCurrency());

        $id = $apiContainer->getSessionId();
        if ($action == CreditApiInterface::ACTIONS['create_order']) {
            $id = $apiContainer->getIncrementId();
        }

        $response = $this->service->makeRequest(
            $url,
            ServiceInterface::SERVICE_KP,
            $body,
            $method,
            $id,
            $action
        );
        if (!isset($response['response_status_code'])) {
            throw new KlarnaException(__('The Klarna API request failed because of a timeout.'));
        }

        $response['response_code'] = $response['response_status_code'];
        return $this->responseFactory->create(['data' => $response]);
    }

    /**
     * Getting back the body
     *
     * @param RequestInterface|null $request
     * @return array
     */
    private function getBody(?RequestInterface $request = null): array
    {
        if ($request) {
            return $request->toArray();
        }

        return [];
    }

    /**
     * Performing the connection
     *
     * @param string $currency
     * @throws KlarnaException
     */
    private function connect(string $currency)
    {
        $this->setUserAgent($this->versionInfo);
        $this->service->setHeader('Accept', '*/*');

        $this->service->connect(
            $this->apiConfiguration->getUserName($this->store, $currency),
            $this->apiConfiguration->getPassword($this->store, $currency),
            $this->apiConfiguration->getApiUrl($this->store, $currency)
        );
    }

    /**
     * Creating the session
     *
     * @param ApiContainer $container
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function createSession(ApiContainer $container)
    {
        $result = $this->processRequest(
            '/payments/' . self::API_VERSION . '/sessions',
            CreditApiInterface::ACTIONS['create_session'],
            $container,
            ServiceInterface::POST
        );

        $container->clear();
        return $result;
    }

    /**
     * Updating the session
     *
     * @param ApiContainer $container
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function updateSession(ApiContainer $container)
    {
        $response = $this->processRequest(
            '/payments/' . self::API_VERSION . '/sessions/' . $container->getSessionId(),
            CreditApiInterface::ACTIONS['update_session'],
            $container,
            ServiceInterface::POST
        );
        if ($response->getResponseCode() === Service::HTTP_NO_CONTENT) {
            return $this->readSession($container);
        }

        $container->clear();
        return $response;
    }

    /**
     * Reading the session
     *
     * @param ApiContainer $container
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function readSession(ApiContainer $container)
    {
        $resp = $this->processRequest(
            '/payments/' . self::API_VERSION . '/sessions/' . $container->getSessionId(),
            CreditApiInterface::ACTIONS['read_session'],
            $container,
            ServiceInterface::GET
        );

        $container->clear();
        $response = $resp->toArray();
        $response['session_id'] = $container->getSessionId();
        return $this->responseFactory->create(['data' => $response]);
    }

    /**
     * Placing the order
     *
     * @param ApiContainer $container
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function placeOrder(ApiContainer $container)
    {
        $this->loggerContainer->setIncrementId($container->getIncrementId());

        $result = $this->processRequest(
            '/payments/' . self::API_VERSION . '/authorizations/' . $container->getAuthorizationToken() . '/order',
            CreditApiInterface::ACTIONS['create_order'],
            $container,
            ServiceInterface::POST
        );

        $container->clear();
        return $result;
    }

    /**
     * Cancelling the order
     *
     * @param ApiContainer $container
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function cancelOrder(ApiContainer $container)
    {
        $result = $this->processRequest(
            '/payments/' . self::API_VERSION . '/authorizations/' . $container->getAuthorizationToken(),
            CreditApiInterface::ACTIONS['cancel_order'],
            $container,
            ServiceInterface::DELETE
        );

        $container->clear();
        return $result;
    }

    /**
     * Set the service User-Agent
     *
     * @param VersionInfo $versionInfo
     * @return void
     */
    private function setUserAgent(VersionInfo $versionInfo): void
    {
        $version = $versionInfo->getModuleVersionString(
            $versionInfo->getVersion('Klarna_Kp'),
            'Klarna_Kp'
        ) . ';';

        if ($this->klarnaQuote !== null && $this->klarnaQuote->isKecSession()) {
            $version .= 'Magento2_KEC/' . $versionInfo->getVersion('Klarna_Kec') . ';';
        }
        $version .= $versionInfo->getFullM2KlarnaVersion();

        $this->service->setUserAgent('Magento2_KP', $version, $versionInfo->getMageInfo());
    }
}
