define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'uiRegistry'
], function ($, ko, Component, customerData, registry) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.cart = customerData.get('cart');
        }
    });
});