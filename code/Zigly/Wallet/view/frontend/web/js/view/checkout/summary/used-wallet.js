define(
    [
       'jquery',
       'Magento_Checkout/js/view/summary/abstract-total',
       'Magento_Checkout/js/model/quote',
       'Magento_Checkout/js/model/totals',
       'Magento_Catalog/js/price-utils'
    ],
    function ($, Component, quote, totals, priceUtils) {
        "use strict";
        return Component.extend({
            totals: quote.getTotals(),
            isDisplayedUsedWalletTotal : function () {
                return totals.getSegment('zwallet') && parseInt(totals.getSegment('zwallet').value) >= 1;
            },
            getUsedWalletLabel : function () {
                let lable = totals.getSegment('zwallet').title;
                return lable;
            },
            getUsedWalletTotal : function () {
                var price = 0;
                if (this.totals() && totals.getSegment('zwallet') && parseInt(totals.getSegment('zwallet').value) >= 1) {
                    price = parseInt(totals.getSegment('zwallet').value);
                    price = Math.floor(price)
                }

                return this.getFormattedPrice(price);
            },
         });
    }
);
