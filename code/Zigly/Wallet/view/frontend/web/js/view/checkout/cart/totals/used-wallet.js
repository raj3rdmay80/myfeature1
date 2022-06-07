define(
    [
        'Zigly_Wallet/js/view/checkout/summary/used-wallet',
       'Magento_Checkout/js/model/totals',
       'Magento_Customer/js/customer-data',
       'Magento_Catalog/js/price-utils'
    ],
    function (Component, totals, customerData, priceUtils) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isDisplayed: function () {
                return true;
            },
            getShippingAmount : function () {
                let cartData = customerData.get('cart');
                let shipamount = cartData().zg_ship_amount;
                if (shipamount) {
                    return shipamount;
                    // shipamount = cartData().zg_ship_amount
                }
                return false;
                // console.log(cartData().website_id)
                // console.log(cartData().zg_ship_amount)
                // customerData.get('cart').subscribe(function (cartInfo) {
                //     shipamount = cartInfo['zg_ship_amount'];
                //     console.log(cartInfo['zg_ship_amount']);
                // }, this);
                // console.log(shipamount)

                // if (totals.totals()) {
                //     var shippingAmount = parseFloat(totals.totals()['shipping_amount']);
                //     return this.getFormattedPrice(shippingAmount);
                // }
                return shipamount;
            }
            // initialize: function () {
            //     var self = this,
            //         cartData = customerData.get('cart');

            //     this.update(cartData());
            //     cartData.subscribe(function (updatedCart) {
            //         addToCartCalls--;
            //         this.isLoading(addToCartCalls > 0);
            //         sidebarInitialized = false;
            //         this.update(updatedCart);
            //         initSidebar();
            //     }, this);
            // }
            // getUsedWalletLabel : function () {
            //     let lable = totals.getSegment('zwallet').title;
            //     return lable;
            // },
            // getUsedWalletTotal : function () {
            //     var price = 0;
            //     if (this.totals() && totals.getSegment('zwallet') && parseInt(totals.getSegment('zwallet').value) >= 1) {
            //         price = parseInt(totals.getSegment('zwallet').value);
            //         price = Math.floor(price)
            //     }

            //     return this.getFormattedPrice(price);
            // },
        });
    }
);