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

        $(document).on('click', '.btn-payment-amount', function() {
            $("#errormsg").remove();
            let addAmount = "";
            if ($.isNumeric($("input[type='text'][name='amount']").val())) {
                addAmount = $("input[type='text'][name='amount']").val();
                if (!addAmount) {
                    $('<div class="message-error error message" id="errormsg">Amount is required.</div>').insertBefore('.choose-amount');
                    return false;
                }
            } else if ($("input[type='radio'][name='add_amount']").length) {
                addAmount = $("input[type='radio'][name='add_amount']:checked").val();
                if (!addAmount) {
                    $('<div class="message-error error message" id="errormsg">Amount is required.</div>').insertBefore('.choose-amount');
                    return false;
                }
            }

            if (!(addAmount >= config.minTotal)) {
                $('<div class="message-error error message" id="errormsg">Wallet recharge can be greater than or equal to '+config.min+'.</div>').insertBefore('.choose-amount');
                return false;
            } else if (!(addAmount <= config.maxTotal)) {
                $('<div class="message-error error message" id="errormsg">Wallet recharge can be less than or equal to '+config.max+'.</div>').insertBefore('.choose-amount');
                return false;
            }

            let placeData = {
                mode: addAmount
            };

            $.ajax({
                url: urlBuilder.build('wallet/index/save'),
                method: "POST",
                data: placeData,
                showLoader: true,
                success: function(response) {
                    if (response.success) {
                        if (response.razorData) {
                            var options = {
                                "key": response.razorConfig.key,
                                "name": response.razorConfig.name,
                                "amount": response.razorData.amount,
                                "currency": "INR",
                                "order_id": response.razorData.razorId,
                                "callback_url": urlBuilder.build('wallet/index/paynow'),
                                "prefill": {
                                    "name": response.razorConfig.customerName,
                                    "email": response.razorConfig.customerEmail,
                                    "contact": response.razorConfig.customerPhoneNo
                                },
                                "notes": {
                                    "merchant_order_id": response.razorData.orderId
                                },
                                "theme": {
                                    "color": "#3399cc"
                                }
                            };
                            var rzp1 = new Razorpay(options);
                            rzp1.open();
                        } else {
                            window.location.href = urlBuilder.build('wallet/index/index')
                        }
                    } else {
                        $('<div class="message-error error message" id="errormsg">' + response.message + '</div>').insertBefore('.wallet-amount');
                    }
                }
            });
        });
        $(document).ready(function (){
            setTimeout(function() {
                console.log(require('Magento_Customer/js/customer-data').reload(['customer']))
            }, 600);
        });
    }
});