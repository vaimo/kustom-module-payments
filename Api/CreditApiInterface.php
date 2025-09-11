<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Model\Api\Container;

/**
 * @api
 */
interface CreditApiInterface
{

    public const ACTIONS = [
        'cancel_order'           => 'Cancel order',
        'create_order'           => 'Create Order',
        'create_session'         => 'Create session',
        'update_session'         => 'Update session',
        'read_session'           => 'Read session',
        'authorize_callback'     => 'Authorize Callback'
    ];

    /**
     * Creating the session
     *
     * @param Container $container
     * @return ResponseInterface
     */
    public function createSession(Container $container);

    /**
     * Updating the session
     *
     * @param Container $container
     * @return ResponseInterface
     */
    public function updateSession(Container $container);

    /**
     * Reading the session
     *
     * @param Container $container
     * @return ResponseInterface
     */
    public function readSession(Container $container);

    /**
     * Placing the order
     *
     * @param Container $container
     * @return ResponseInterface
     */
    public function placeOrder(Container $container);

    /**
     * Cancelling the order
     *
     * @param Container $container
     * @return ResponseInterface
     */
    public function cancelOrder(Container $container);
}
