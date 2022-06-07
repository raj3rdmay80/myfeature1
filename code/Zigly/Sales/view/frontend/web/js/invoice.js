/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
define(['jquery'], function ($) {
    'use strict';

    return function (config) {
        $(document).ready(function(){
            $(document).on('click', '.order-generate-invoice', function(e){
                $("#errormsg").remove();
                e.preventDefault();
                var orderId = $(this).attr('key');
                var invoiceUrl = config.invoiceUrl;
                $.ajax({
                    url: invoiceUrl,
                    method: 'POST',
                    data: {'orderid': orderId},
                    showLoader: true,
                    success: function (response) {
                        console.log(response);
                        if (response.success) {
                            window.open(response.invoice, '_blank');
                        } else {
                            $('<div class="message-error error message" id="errormsg">' + response.message + '</div>').insertBefore('.columns');
                        }
                    }
                });
            });
            $(document).on('click', '.service-generate-invoice', function(e){
                $("#errormsg").remove();
                e.preventDefault();
                var bookingId = $(this).attr('key');
                var invoiceBookingUrl = config.invoiceBookingUrl;
                $.ajax({
                    url: invoiceBookingUrl,
                    method: 'POST',
                    data: {'bookingId': bookingId},
                    showLoader: true,
                    success: function (response) {
                        console.log(response);
                        if (response.success) {
                            window.open(response.invoice, '_blank');
                        } else {
                            $('<div class="message-error error message" id="errormsg">' + response.message + '</div>').insertBefore('.columns');
                        }
                    }
                });
            });
        });
    }
});
