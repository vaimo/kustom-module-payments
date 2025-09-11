<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api;

use Klarna\Kp\Api\Data\RequestInterface;

/**
 * @internal
 */
class Container
{
    /**
     * @var string
     */
    private string $currency = '';
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var string
     */
    private string $sessionId = '';
    /**
     * @var string
     */
    private string $authorizationToken = '';
    /**
     * @var string
     */
    private string $incrementId = '';

    /**
     * Getting back the session id
     *
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Setting the session id
     *
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Getting back the currency
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Setting the currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Getting back the request
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Setting the request
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Getting back the authorization token
     *
     * @return string
     */
    public function getAuthorizationToken(): string
    {
        return $this->authorizationToken;
    }

    /**
     * Setting the authorization token
     *
     * @param string $authorizationToken
     * @return $this
     */
    public function setAuthorizationToken(string $authorizationToken): self
    {
        $this->authorizationToken = $authorizationToken;
        return $this;
    }

    /**
     * Getting back the increment id
     *
     * @return string
     */
    public function getIncrementId(): string
    {
        return $this->incrementId;
    }

    /**
     * Setting the increment id
     *
     * @param string $incrementId
     * @return $this
     */
    public function setIncrementId(string $incrementId): self
    {
        $this->incrementId = $incrementId;
        return $this;
    }

    /**
     * Clearing the container
     */
    public function clear(): void
    {
        $this->currency = '';
        $this->sessionId = '';
        $this->authorizationToken = '';
        $this->incrementId = '';
    }
}
