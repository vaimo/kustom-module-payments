<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization;

use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;

/**
 * @internal
 */
class DuplicateRequestBlocker
{
    /**
     * @var array
     */
    private array $lastSessionRequestPayload = [];

    /**
     * Check if the pre build request is a duplicate
     *
     * For comparison we remove the merchant_urls from the request,
     * as they have a unique session_id for each create_session request.
     *
     * @param RequestInterface $sessionRequest
     * @return bool
     */
    public function isDuplicateRequest(RequestInterface $sessionRequest): bool
    {
        $sessionRequestArray = $sessionRequest->toArray();
        unset($sessionRequestArray['merchant_urls']);

        if ($this->lastSessionRequestPayload === $sessionRequestArray) {
            return true;
        }

        return false;
    }

    /**
     * Handling the Klarna response
     *
     * @param ResponseInterface $klarnaResponse
     * @param RequestInterface $createSessionRequest
     * @throws KlarnaApiException
     */
    public function handleKlarnaResponse(
        ResponseInterface $klarnaResponse,
        RequestInterface $createSessionRequest
    ): void {
        $createSessionArray = $createSessionRequest->toArray();
        unset($createSessionArray['merchant_urls']);

        $this->lastSessionRequestPayload = $createSessionArray;

        if (!$klarnaResponse->isSuccessfull()) {
            throw new KlarnaApiException(__('Unable to initialize Klarna payments session.'));
        }
    }
}
