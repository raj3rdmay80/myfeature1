/*global define*/
define([
    'Magento_Ui/js/form/form',
    'jquery',
    'https://maps.googleapis.com/maps/api/js?key=AIzaSyD5FjMnlM2QHBs6Ffi3boG0GaiYatxkgQM&libraries=places',
    'jquery/jquery.cookie',
], function(Component, $) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();
            return this;
        },
       
        formRendered: function() {
            var existPostCode = setInterval(function() {
                if ($('.form-shipping-address [name="postcode"]').length) { 
                    clearInterval(existPostCode);
                    let postcode = $('.form-shipping-address [name="postcode"]').val()
                    $('.form-shipping-address [name="postcode"]').val('').change()
                    $('.form-shipping-address [name="postcode"]').val(postcode).change()
                }
                $(window).scrollTop(0)
                $("input#customer-email").prop("placeholder", "Enter Email")
            }, 100);
        },
    });
});
