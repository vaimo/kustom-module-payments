/* global Klarna */
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Klarna_Kp/js/model/config',
        'Magento_Checkout/js/view/billing-address',
        'Magento_Checkout/js/action/create-billing-address',
        'mage/storage',
        'mage/url',
        'klarnapi'
    ],
    function ($, quote, customer, config, viewBillingAddress, createBillingAddress, storage, urlBuilder) {
        'use strict';

        var isBillingSameAsShipping;

        viewBillingAddress().
            isAddressSameAsShipping.
            subscribe(function (isSame) {
                isBillingSameAsShipping = isSame;
            });

        return {
            b2bEnabled: config.b2bEnabled,

            /**
             * Getting back the session data from the backend
             *
             * @returns {Promise<{}|*>}
             */
            getSessionDataFromBackend: async function (isVirtual, billingAddressEmail) {
                const fieldName = customer.isLoggedIn() ? 'quoteId' : 'maskedQuoteId';

                let backendSessionResponse = {};

                try {
                    const response = await storage.get(
                            `${config.currentSessionDataUrl}?${fieldName}=${quote.getQuoteId()}`,
                            false, 'application/json'),

                        responseData = response.data;

                    backendSessionResponse = {
                        billing_address: responseData.billing_address,
                        customer: responseData.customer
                    };

                    if (!isVirtual) {
                        backendSessionResponse.shipping_address = Object.assign(
                            {email: billingAddressEmail},
                            responseData.shipping_address);
                    }
                } catch (error) {
                }

                return backendSessionResponse;
            },

            /**
             * Getting back the address based on the input
             * @param {Array} address
             * @param {String} email
             * @returns {{
             *      street_address: String,
             *      country: String,
             *      city: String,
             *      phone: String,
             *      organization_name: String,
             *      given_name: String,
             *      postal_code: String,
             *      family_name: String,
             *      email: *
             * }}
             */
            buildAddress: function (address, email) {
                var addr = {
                    organization_name: '',
                    given_name: '',
                    family_name: '',
                    street_address: '',
                    city: '',
                    postal_code: '',
                    country: '',
                    phone: '',
                    email: email
                };

                if (!address) { // Somehow we got a null passed in
                    return addr;
                }

                if (address.prefix) {
                    addr.title = address.prefix;
                }

                if (address.firstname) {
                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    addr.given_name = address.firstname;
                }

                if (address.lastname) {
                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    addr.family_name = address.lastname;
                }

                if (address.street) {
                    if (address.street.length > 0) {
                        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                        addr.street_address = address.street[0];
                    }

                    if (address.street.length > 1) {
                        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                        addr.street_address2 = address.street[1];
                    }
                }

                if (address.city) {
                    addr.city = address.city;
                }

                if (address.regionCode) {
                    addr.region = address.regionCode;
                }

                if (address.postcode) {
                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    addr.postal_code = address.postcode;
                }

                if (address.countryId) {
                    addr.country = address.countryId;
                }

                if (address.telephone) {
                    addr.phone = address.telephone;
                }

                // Having organization_name in the billing address causes KP/PLSI to return B2B methods
                // no matter the customer type. So we only want to set this if the merchant has enabled B2B.
                if (address.company && this.b2bEnabled) {
                    addr['organization_name'] = address.company;
                }

                return addr;
            },

            /**
             * Getting back the customer
             * @param {Object} billingAddress
             * @returns {{type: String}}
             */
            buildCustomer: function (billingAddress) {
                var type = 'person';

                if (this.b2bEnabled && billingAddress &&
                    billingAddress.company) {
                    type = 'organization';
                }

                return {
                    'type': type
                };
            },

            getSessionDataFromFrontend: function (isVirtual) {
                let email = '',
                    shippingAddress = quote.shippingAddress(),
                    data = {
                        'billing_address': {},
                        'shipping_address': {},
                        'customer': {}
                    },
                    customFormSelector = '.payment-method.klarna-payments-method._active .billing-address-form form',
                    billingAddress;

                email = customer.isLoggedIn()
                    ? customer.customerData.email
                    : quote.guestEmail;

                if (isVirtual) {
                    shippingAddress = quote.billingAddress();
                }

                customFormSelector = '.payment-method.klarna-payments-method._active .billing-address-form form';
                billingAddress = this.getBillingAddress(customFormSelector);

                // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                data.billing_address = this.buildAddress(billingAddress, email);
                data.shipping_address = this.buildAddress(shippingAddress,
                    email);
                if (!!data.billing_address && !!data.shipping_address) {
                    data.shipping_address.organization_name = data.billing_address.organization_name;
                }
                data.customer = this.buildCustomer(billingAddress);

                if (isVirtual) {
                    delete data.shipping_address;
                }

                return data;
            },

            /**
             * Getting back data for performing a Klarna update request
             * @returns {Promise}
             */
            getCustomerDataFromSession: async function () {
                const isVirtual = quote.isVirtual(),
                    frontendData = this.getSessionDataFromFrontend(isVirtual);

                try {
                    return await this.getSessionDataFromBackend(isVirtual, frontendData.billing_address.email);
                } catch (error) {
                }

                return frontendData;
            },

            /**
             * Performing the Klarna load request to load the Klarna widget
             * @param {String} paymentMethod
             * @param {String} containerId
             * @param {Function} callback
             */
            load: function (paymentMethod, containerId, callback) {
                var promiseData = $.Deferred().resolve(null).promise();

                if ($('#' + containerId).length) {

                    if (config.dataSharingOnload) {
                        promiseData = this.getCustomerDataFromSession();
                    }

                    if (config.isKecSession) {
                        paymentMethod = null;
                    }

                    promiseData.then(function (sessionData) {
                        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                        Klarna.Payments.load(
                            {
                                payment_method_category: paymentMethod,
                                container: '#' + containerId
                            }, sessionData, function (res) {
                                var errors = false;

                                if (res.errors) {
                                    errors = true;
                                }
                                config.hasErrors(errors);

                                if (callback) {
                                    callback(res);
                                }
                            }
                        );
                    });
                }
            },

            /**
             * Initiating Klarna to add the javascript SDK to the page
             */
            init: function () {
                // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                Klarna.Payments.init({
                    client_token: config.clientToken()
                });
            },

            /**
             * Sending the Klarna authorize request
             * @param {String} paymentMethod
             * @param {{[p: string]: *}} data
             * @param {Function} callback
             */
            authorize: async function (paymentMethod, data, callback) {
                if (config.isKecSession) {
                    // eslint-disable-next-line vars-on-top
                    var payload = await this.getFinalizePayload();

                    // Only update if we get a payload back
                    if (payload) {
                        data = {...payload, ...data};
                    }

                    Klarna.Payments.finalize({}, data, function (res) {
                        var errors = false;

                        if (res.errors) {
                            errors = true;
                        }
                        config.hasErrors(errors);
                        callback(res);
                    });
                } else {
                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    Klarna.Payments.authorize(
                        {
                            payment_method_category: paymentMethod
                        }, data, function (res) {
                            var errors = false;

                            if (res.errors) {
                                errors = true;
                            }
                            config.hasErrors(errors);
                            callback(res);
                        }
                    );
                }
            },

            /**
             * Get the billingAddress value
             */
            getBillingAddress: function (formSelector) {
                if (isBillingSameAsShipping) {
                    return quote.shippingAddress();
                }

                if (!this.addressIsEmpty(quote.billingAddress())) {
                    return quote.billingAddress();
                }

                const billingAddressForm = this.detectBillingAddressForm(
                        formSelector),
                    billingAddressData = this.getBillingAddressFormData(
                        billingAddressForm);

                if (this.addressIsEmpty(billingAddressData)) {
                    return quote.shippingAddress();
                }

                return createBillingAddress(billingAddressData);
            },

            /**
             * Select billing address form and return the form
             * @param formSelector
             * @returns {jQuery|HTMLElement|*}
             */
            detectBillingAddressForm: function (formSelector) {
                const defaultMagentoFormSelector = '.billing-address-form form',
                    form = $(formSelector);

                if (form.length > 0) {
                    return form;
                }

                return $(defaultMagentoFormSelector);
            },

            /**
             * Extract data from the html form element
             * @param billingAddressForm
             * @returns {*}
             */
            getBillingAddressFormData: function (billingAddressForm) {
                const fields = $(billingAddressForm).serializeArray();

                // create address object from array
                return fields.reduce(function (result, field) {
                    let name = field.name,
                        value = field.value,
                        // select `address[0]` and remove the `[0]` part
                        selectCounterBracketRegex = /\[\d+\]/g;

                    if (selectCounterBracketRegex.test(name)) {
                        name = name.replace(selectCounterBracketRegex, '');
                        value = result[name] && Array.isArray(result[name])
                            ? result[name]
                            : [];
                        value.push(field.value);
                    }

                    result[name] = value;

                    return result;
                }, {});
            },

            /**
             * check if the address is empty or not
             * @param address
             * @returns {this is string[]}
             */
            addressIsEmpty: function (address) {
                const properties = [
                    'city',
                    'company',
                    'firstname',
                    'lastname',
                    'postcode',
                    'street',
                    'telephone'
                ];

                if (!address) {
                    return true;
                }

                return properties.every(function (propertyName) {
                    return !address[propertyName];
                });
            },

            /**
             * Get the payload for finalizing an Express Checkout order
             * @returns {Promise<null|{order_lines: Array<object>, order_tax_amount: number, order_amount: number}>}
             */
            getFinalizePayload: async function () {
                var form = new FormData(),
                    payload;

                form.set('additional_input', JSON.stringify({
                    'use_existing_quote': 1
                }));
                if (quote.shippingMethod() !== null) {
                    form.set('shipping_method', quote.shippingMethod().method_code.toString());
                    form.set('shipping_carrier_code', quote.shippingMethod().carrier_code.toString());
                }

                try {
                    payload = $.ajax({
                        url: urlBuilder.build('checkout/klarna/getPayLoad'),
                        data: form,
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,

                        success: function (result) {
                            return result ? result : null;
                        },
                        error: function () {
                            return null;
                        }
                    }).then(function (result) {
                        if (result) {
                            return {
                                order_lines: result.order_lines,
                                order_tax_amount: result.order_tax_amount,
                                order_amount: result.order_amount
                            };
                        }
                        return null;
                    });
                } catch (error) {
                    payload = null;
                }
                return payload;
            }
        };
    }
);
