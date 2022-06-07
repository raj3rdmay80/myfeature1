define([
    'ko',
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/quote',
    // 'Magento_Checkout/js/model/error-processor',errorProcessor,
    'mage/translate',
    'mage/url',
    'Magento_Checkout/js/model/full-screen-loader'
], function (ko, $, Component, totals, getPaymentInformationAction, paymentService, priceUtils, quote, $t, urlBuilder, fullScreenLoader) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Zigly_Wallet/checkout/wallet',
        },

        /**
         * @return {*}
         */
        isWalletAvailable: function () {
            // console.log(paymentService.getAvailablePaymentMethods().length)
            // console.log(paymentService.getAvailablePaymentMethods().length > 0)
            // console.log(window.checkoutConfig.zwallet_amount)
            return window.checkoutConfig.zwallet_amount;
            // return this.isFullMode();
        },

        totals: quote.getTotals(),

        initObservable: function () {
            console.log('ININNININIwalllet Wwalllet')
            // console.log(totals)
            // console.log(totals.getSegment('zwallet'))
            // console.log(this.totals())
            // console.log(totals.totals())
            // totals.totals()
            if (totals.getSegment('zwallet')) {
                this._super().observe({toggleWallet: ko.observable(true)});
            } else {
                this._super().observe({toggleWallet: ko.observable(false)});

            }

            // this._super().observe(['toggleWallet']);

            this.toggleWallet.subscribe(function (isChecked) {
                let url = urlBuilder.build('rest/V1/carts/mine/zwallet/')+'40';
                fullScreenLoader.startLoader();
                $.ajax({
                    url: urlBuilder.build('wallet/checkout/togglewallet'),
                    method: "POST",
                    data: {'is_checked': isChecked},
                    dataType: 'json',
                    showLoader: true,
                    success: function(response) {
                        /*if (response.success) {
                        } else {
                        }*/
                        var deferred,
                            pointsUsed = 0;

                            deferred = $.Deferred();

                            // if (pointsUsed > 0) {
                            //     isApplied(true);
                                totals.isLoading(true);
                                getPaymentInformationAction(deferred);

                                $.when(deferred).done(function () {
                                    // points((pointsUsed).toFixed(2));
                                    // pointsLeftObs((pointsLeftObs() - points()).toFixed(2));
                                    // $('#amreward_amount').val(points()).change();

                                    fullScreenLoader.stopLoader();
                                    totals.isLoading(false);
                                });
                            // }
                            fullScreenLoader.stopLoader();
                        // }
                        // var deferred = $.Deferred();
                        // getTotalsAction([], deferred);
                            fullScreenLoader.stopLoader();
                        
                    }
                });
            });
            // totals.totals.subscribe(this.getHighlightData.bind(this));
            return this;
        },

        getWalletAmount: function () {
            return this.getFormattedPrice(window.checkoutConfig.zwallet_amount);
        }
    });
});
