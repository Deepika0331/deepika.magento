
define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function ($, ko, quote, $t) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                shippingMethodItemTemplate: 'Meticulosity_CollectShipping/shipping-address/shipping-method-item'
            },

            isCollectShippingVisible: ko.observable(false),

            initialize: function () {
                var self = this;
                quote.shippingMethod.subscribe(function (value) {
                    self.isCollectShippingVisible(value.carrier_code === 'collect_shipping')
                }, this)
                this._super();
            },

            isCollectShipping: function (method) {
                return method.carrier_code === 'collect_shipping';
            },

            getCustomCarrier: function () {
                if (quote.shippingMethod() && quote.shippingMethod().extension_attributes !== undefined) {
                    return quote.shippingMethod().extension_attributes.custom_carrier;
                }
                return null;
            },

            getCustomAccountNumber: function () {
                if (quote.shippingMethod() && quote.shippingMethod().extension_attributes !== undefined) {
                    return quote.shippingMethod().extension_attributes.custom_account_number;
                }
                return null;
            },

            getCustomCarrierOptions: function () {
                if (window.checkoutConfig['collect_shipping']['carrier_options'] !== undefined) {
                    return window.checkoutConfig['collect_shipping']['carrier_options'];
                }
                return [];
            },

            validateShippingInformation: function () {
                if (quote.shippingMethod() && quote.shippingMethod().carrier_code === 'collect_shipping') {
                    var customCarrier = $('[name="extension_attributes[custom_carrier]"]').val();
                    var customAccountNumber = $('[name="extension_attributes[custom_account_number]"]').val();
                    if (!customCarrier) {
                        this.errorValidationMessage(
                            $t('The Carrier is missing. Set the Carrier and try again.')
                        );

                        return false;
                    }

                    if (!customAccountNumber) {
                        this.errorValidationMessage(
                            $t('The Account Number is missing. Set the Account Number and try again.')
                        );

                        return false;
                    }
                }

                return this._super();
            },

            getCollectShippingCarriers: function () {
                var result = [{carrier: ''}];
                var optionsData = this.getCustomCarrierOptions();

                _.each(optionsData, function (item) {
                    result.push({
                        'carrier': item.carrier,
                    });
                });

                return result;
            },
        });
    }
});
