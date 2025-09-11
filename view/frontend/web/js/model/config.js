/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define(
    [
        'ko'
    ], function (ko) {
        'use strict';

        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
        var clientToken = ko.observable(
                window.checkoutConfig.payment.klarna_kp.client_token),
            message = window.checkoutConfig.payment.klarna_kp.message,
            enabled = window.checkoutConfig.payment.klarna_kp.enabled,
            b2bEnabled = window.checkoutConfig.payment.klarna_kp.b2b_enabled,
            success = window.checkoutConfig.payment.klarna_kp.success,
            hasErrors = ko.observable(false),
            availableMethods = window.checkoutConfig.payment.klarna_kp.available_methods,
            redirectUrl = window.checkoutConfig.payment.klarna_kp.redirect_url,
            reloadConfigUrl = window.checkoutConfig.payment.klarna_kp.reload_checkout_config_url,
            getQuoteStatusUrl = window.checkoutConfig.payment.klarna_kp.get_quote_status_url,
            updateQuoteEmailUrl = window.checkoutConfig.payment.klarna_kp.update_quote_email_url,
            authorizationTokenUpdateUrl = window.checkoutConfig.payment.klarna_kp.authorization_token_update_url,
            currentSessionDataUrl = window.checkoutConfig.payment.klarna_kp.current_session_data_url,
            isKecSession = window.checkoutConfig.payment.klarna_kp.is_kec_session;

        return {
            hasErrors: hasErrors,
            enabled: enabled,
            b2bEnabled: b2bEnabled,
            clientToken: clientToken,
            message: message,
            success: success,
            availableMethods: availableMethods,
            redirectUrl: redirectUrl,
            reloadConfigUrl: reloadConfigUrl,
            getQuoteStatusUrl: getQuoteStatusUrl,
            updateQuoteEmailUrl: updateQuoteEmailUrl,
            currentSessionDataUrl: currentSessionDataUrl,
            authorizationTokenUpdateUrl: authorizationTokenUpdateUrl,
            isKecSession: isKecSession
        };
    }
);
