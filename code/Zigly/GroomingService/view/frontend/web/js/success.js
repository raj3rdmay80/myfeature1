/**
 * Copyright (C) 2020  Zigly


 * @package Zigly_GroomingService
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'moment',
    'mage/url',
    'Magento_Ui/js/modal/confirm',
    'mage/validation',
    'jquery/ui',
    'jquery/jquery.cookie',
    'mage/translate',
    "domReady!"
], function($, modal, moment, urlBuilder, confirmation) {
    'use strict';

    return function(config) {

        $(document).ready(function (){
            setTimeout(function() {
                console.log(require('Magento_Customer/js/customer-data').reload(['customer']))
            }, 600);
        });
    }
});