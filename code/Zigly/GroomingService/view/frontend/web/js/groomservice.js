/**
 * Copyright (C) 2020  Zigly


 * @package Zigly_GroomingService
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Catalog/js/price-utils',
    'moment',
    'mage/url',
    'Magento_Ui/js/modal/confirm',
    'mage/validation',
    'Zigly_GroomingService/js/lib/fullcalendar.min',
    'https://maps.googleapis.com/maps/api/js?key=AIzaSyBaWv8RYNF_wfDNIKrz3MzlfD_WKYfUDnI',
    'jquery/ui',
    'jquery/jquery.cookie',
    'mage/translate',
    "domReady!"
], function($, modal, priceUtils, moment, urlBuilder, confirmation) {
    'use strict';

    return function(config) {
        var priceFormat = {
            decimalSymbol: '.',
            groupLength: 3,
            groupSymbol: ",",
            integerRequired: false,
            pattern: config.currencysymbol + "%s",
            precision: 0,
            requiredPrecision: 0
        };

        $(document).ready(function(){
            var url = window.location.href;
            var pet = url.split('?').pop().split('=').pop();
            var active = pet.replace('/', '');
            if (active && $.isNumeric(active)) {
                $('.'+active).addClass('pet-active');
            }
        });
        $(document).on('click', '.steps',function(){
            $("#errormsg").remove();
            $('.steps').removeClass('current-tab');
            $(this).addClass('current-tab');
            if ($('.current-tab > .step-name > .selected-pet').next().hasClass("selected-pet-value")) {
                if (!$("input[name='select_pet']").val()) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a pet to continue.</div>').insertAfter('#progressbar');
                    return false;
                }
                $('.field2, .field3, .field4, .field5').removeClass('current').css('display', 'none');
                $('.field1').addClass('current');
                $('.current').css('display', 'block');
                $(this).parent().addClass('active');
                $('#progressbar').removeClass('move-left');
                $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                $('#progressbar li.active').prevAll('li').addClass('preve');
                $(this).parent().prev('li').removeClass('preve');
                $(this).parent().nextAll('li').removeClass("active");
                $(this).parent().nextAll('li').removeClass("hide-lable");
                $(this).parent().nextAll('li').removeClass("preve");
                $(this).parent().removeClass('hide-lable');
                $(this).parent().removeClass('preve');
                $(window).scrollTop(0);
            } else if ($('.current-tab > .step-name > .selected-plan').next().hasClass("selected-plan-value")) {
                if (!$('input[name="plan_id"]').val()) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a plan.</div>').insertAfter('#progressbar');
                    return false;
                }
                $('.field1, .field3, .field4, .field5').removeClass('current').css('display', 'none');
                $('.field2').addClass('current');
                $('.current').css('display', 'block');
                $(this).parent().addClass('active');
                var leftPos = $('ul.grooming-container-width').scrollLeft();
                var width = $(window).width();
                console.log(leftPos);
                if (parseInt(width) < 768) {
                    $("ul.grooming-container-width").animate({
                        scrollLeft: leftPos + 185
                    }, 800);
                }
                $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                $('#progressbar li.active').prevAll('li').addClass('preve');
                $(this).parent().prev('li').removeClass('preve');
                $(this).parent().nextAll('li').removeClass("active");
                $(this).parent().nextAll('li').removeClass("hide-lable");
                $(this).parent().nextAll('li').removeClass("preve");
                $(this).parent().removeClass('hide-lable');
                $(this).parent().removeClass('preve');
                $(window).scrollTop(0);
            } else if ($('.current-tab > .step-name > .selected-time').next().hasClass("selected-time-value")) {
                if (!$('input[name="selected_date"]').val() || !$('input[name="selected_time"]').val()) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a date and time.</div>').insertAfter('#progressbar');
                    return false;
                }
                $('.field1, .field2, .field4, .field5').removeClass('current').css('display', 'none');
                $('.field3').addClass('current');
                $('.current').css('display', 'block');
                $(this).parent().addClass('active');
                var leftPos = $('ul.grooming-container-width').scrollLeft();
                var width = $(window).width();
                console.log(leftPos);
                if (parseInt(width) < 768) {
                    $("ul.grooming-container-width").animate({
                        scrollLeft: leftPos + 370
                    }, 800);
                }
                $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                $('#progressbar li.active').prevAll('li').addClass('preve');
                $(this).parent().prev('li').removeClass('preve');
                $(this).parent().nextAll('li').removeClass("active");
                $(this).parent().nextAll('li').removeClass("hide-lable");
                $(this).parent().nextAll('li').removeClass("preve");
                $(this).parent().removeClass('hide-lable');
                $(this).parent().removeClass('preve');
                $(window).scrollTop(0);
            } else if ($('.current-tab > .step-name > .selected-address').next().hasClass("selected-address-value")) {
                if (!$("input[name='select_detail']").val()) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a address.</div>').insertAfter('#progressbar');
                    return false;
                }
                $('.field1, .field2, .field3, .field5').removeClass('current').css('display', 'none');
                $('.field4').addClass('current');
                $('.current').css('display', 'block');
                $(this).parent().addClass('active');
                var leftPos = $('ul.grooming-container-width').scrollLeft();
                var width = $(window).width();
                console.log(leftPos);
                if (parseInt(width) < 768) {
                    $("ul.grooming-container-width").animate({
                        scrollLeft: leftPos + 555
                    }, 800);
                }
                $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                $('#progressbar li.active').prevAll('li').addClass('preve');
                $(this).parent().prev('li').removeClass('preve');
                $(this).parent().nextAll('li').removeClass("active");
                $(this).parent().nextAll('li').removeClass("hide-lable");
                $(this).parent().nextAll('li').removeClass("preve");
                $(this).parent().removeClass('hide-lable');
                $(this).parent().removeClass('preve');
                $(window).scrollTop(0);
            } else if ($("input[name='select_pet']").val() && $('input[name="plan_id"]').val() && $('input[name="selected_date"]').val() && $('input[name="selected_time"]').val() && $("input[name='select_detail']").val()) {
                $('.field1, .field2, .field3, .field4').removeClass('current').css('display', 'none');
                $('.field5').addClass('current');
                $('.current').css('display', 'block');
                $(this).parent().addClass('active');
                var leftPos = $('ul.grooming-container-width').scrollLeft();
                var width = $(window).width();
                console.log(leftPos);
                if (parseInt(width) < 768) {
                    $("ul.grooming-container-width").animate({
                        scrollLeft: leftPos + 740
                    }, 800);
                }
                $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                $('#progressbar li.active').prevAll('li').addClass('preve');
                $(this).parent().prev('li').removeClass('preve');
                $(this).parent().nextAll('li').removeClass("active");
                $(this).parent().nextAll('li').removeClass("hide-lable");
                $(this).parent().nextAll('li').removeClass("preve");
                $(this).parent().removeClass('hide-lable');
                $(this).parent().removeClass('preve');
                $(window).scrollTop(0);
            }
        });
        $(document).on('click', '.time-proceed',function(){
            $("#errormsg").remove();
            if (!$('input[name="selected_date"]').val() || !$('input[name="selected_time"]').val()) {
                $('<div class="message-error error message groom" id="errormsg">Please select a date and time.</div>').insertAfter('#progressbar');
                return false;
            }
            renderAddress();
            $('#progressbar').removeClass('move-left');
            if (jQuery(window).width() > 768 ) {
                $('#progressbar').addClass('move-left');
            }
            $('.current').removeClass('current').hide().next().show().addClass('current');
            $('#progressbar li.active').next().addClass('active');
            $('#progressbar li.active').removeClass('hide-lable').prev().addClass('hide-lable');
            $('#progressbar li.active').prevAll('li').addClass('preve');
            $('#progressbar li.active').last().prev('li').removeClass('preve');
        });
        $(document).on('click', '.select-pet-action', function() {
            $("#errormsg").remove();
            $('li.pet-listing').removeClass('pet-active');
            $(this).parents('li').addClass('pet-active');
            let selectPetId = $("input[name='select_pet']").val($(this).val());
            let postPet = {
                petid: $(this).val()
            };
            $.ajax({
                url: config.getplan,
                method: "POST",
                data: postPet,
                showLoader: true,
                success: function(response) {
                    $(".selected-pet-value").remove();
                    $(".selected-pet").text("Selected Pet(s)");
                    $("h3.plan-pet-name").text("Select A Plan For "+response.pet_name+"");
                    $('<p class="step-value selected-pet-value">'+response.pet_name+'</p>').insertAfter(".selected-pet");
                    $('.field2').html(response.output);
                    $('.next-nav').show()
                    $('.back-nav').show()
                    $('#progressbar').removeClass('move-left');
                    if (jQuery(window).width() > 768 ) {
                        $('#progressbar').addClass('move-left');
                    }
                    $('.current').removeClass('current').hide().next().show().addClass('current');
                    $('#progressbar li.active').next().addClass('active');
                    $('#progressbar li.active').removeClass('hide-lable').prev().addClass('hide-lable');
                    $('#progressbar li.active').prevAll('li').addClass('preve');
                    $('#progressbar li.active').last().prev('li').removeClass('preve');
                }
            });
        });
        $(document).on('click', '.select-pet-action', function() {
            var leftPos = $('ul.grooming-container-width').scrollLeft();
            var width = $(window).width();
            console.log(leftPos);
            $(window).scrollTop(0);
            if (parseInt(width) < 768) {
                $("ul.grooming-container-width").animate({
                    scrollLeft: leftPos + 185
                }, 800);
            }
        });
        $(document).on('click', '.select-address-action', function() {
            $("#errormsg").remove();
            $('li.detail-listing').removeClass('address-active');
            $(this).parents('li').addClass('address-active');
            let selectaddressId = $("input[name='select_detail']").val($(this).val());
            if (!selectaddressId) {
                $('<div class="message-error error message groom" id="errormsg">Please select a address.</div>').insertAfter('#progressbar');
                return false;
            }
            let postAddress = {
                address_id: $(this).val()
            };
            renderReview(postAddress);
            $('.next-nav').hide()
            $('.back-nav').show()
            $('#progressbar').removeClass('move-left');
            if (jQuery(window).width() > 768 ) {
                $('#progressbar').addClass('move-left');
            }
            $('.current').removeClass('current').hide().next().show().addClass('current');
            $('#progressbar li.active').next().addClass('active');
            $('#progressbar li.active').removeClass('hide-lable').prev().addClass('hide-lable');
            $('#progressbar li.active').prevAll('li').addClass('preve');
            $('#progressbar li.active').last().prev('li').removeClass('preve');
        });
        $(document).on('click', '.select-address-action', function() {
            var leftPos = $('ul.grooming-container-width').scrollLeft();
            var width = $(window).width();
            console.log(leftPos);
            $(window).scrollTop(0);
            if (parseInt(width) >= 768) {
                $("ul.grooming-container-width").animate({
                    scrollLeft: leftPos + 500
                }, 800);
            }
        });
        $(document).on('click', '.time-proceed', function() {
            var leftPos = $('ul.grooming-container-width').scrollLeft();
            var width = $(window).width();
            console.log(leftPos);
            $(window).scrollTop(0);
            if (parseInt(width) >= 768) {
                $("ul.grooming-container-width").animate({
                    scrollLeft: leftPos + 500
                }, 800);
            }
        });
        /* Navigation */    
        /*$(document).on('click', '.next-nav', function() {
            $("#errormsg").remove();
            let isPlan = false;
            if ($('.current').hasClass("field1")) {
                let petId = $("input[name='select_pet']").val();
                if (!petId) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a pet to continue.</div>').insertAfter('#progressbar');
                    return false;
                }
                let postPet = {
                    petid: petId
                };
                $.ajax({
                    url: config.getplan,
                    method: "POST",
                    data: postPet,
                    showLoader: true,
                    success: function(response) {
                        $(".selected-pet-value").remove();
                        $(".selected-pet").text("Selected Pet(s)");
                        $('<p class="step-value selected-pet-value">'+response.pet_name+'</p>').insertAfter(".selected-pet");
                        $('.field2').html(response.output);
                    }
                });

            } else if ($('.current').hasClass("field2")) {
                if (!$('input[name="plan_id"]').val()) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a plan.</div>').insertAfter('#progressbar');
                    return false;
                }
                let customActivity = [];
                if ($('input[name="plan_is_custom"]').val() == 2) {
                    if (!$('div.plan[data-planid="' + $('input[name="plan_id"]').val() + '"]').find('input[type="checkbox"]:checked').length) {
                        $('div.plan[data-planid="' + $('input[name="plan_id"]').val() + '"]').find('.detail-options').show()
                        $('<div class="message-error error message groom" id="errormsg">Please select activities.</div>').insertAfter('div.plan[data-planid="' + $('input[name="plan_id"]').val() + '"] #plan-select-custom')
                        $('<div class="message-error error message groom" id="errormsg">Please select activities.</div>').insertAfter('#progressbar');
                        return false;
                    }
                    $($('div.plan[data-planid="' + $('input[name="plan_id"]').val() + '"]').find('input[type="checkbox"]:checked')).each(function() {
                        customActivity.push($(this).val());
                    });
                }
                let postAddress = {
                    planid: $('input[name="plan_id"]').val(),
                    activity: customActivity
                };
                isPlan = true;
                $.ajax({
                    url: urlBuilder.build('services/grooming/setplan'),
                    method: "POST",
                    data: postAddress,
                    showLoader: true,
                    success: function(response) {
                        if (!response.success) {
                            console.log('plan-select-NEXR')
                            $('.next-nav').hide()
                            $('<div class="message-error error message groom" id="errormsg">'+response.message+'</div>').insertAfter('#progressbar');
                            return false;
                        } else {
                            $(".selected-plan-value").remove();
                            $(".selected-plan").text("Selected Plan");
                            $('<p class="step-value selected-plan-value">'+response.plan_name+'</p>').insertAfter(".selected-plan");
                            $('.plan-booked').html(response.output);
                            $('.next-nav').show()
                            $('.back-nav').show()
                            $('.current').removeClass('current').hide().next().show().addClass('current');
                            $('#progressbar li.active').next().addClass('active');
                            $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                            $('#progressbar li.active').prevAll('li').addClass('preve');
                            $('#progressbar li.active').last().prev('li').removeClass('preve');
                            $(window).scrollTop(0);
                        }
                    }
                });
            } else if ($('.current').hasClass("field3")) {
                if (!$('input[name="selected_date"]').val() || !$('input[name="selected_time"]').val()) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a date and time.</div>').insertAfter('#progressbar');
                    return false;
                }
                renderAddress();
            } else if ($('.current').hasClass("field4")) {
                let addressId = $("input[name='select_detail']").val();
                if (!addressId) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a address.</div>').insertAfter('#progressbar');
                    return false;
                }
                let postAddress = {
                    address_id: addressId
                };
                renderReview(postAddress);
            }
            if (!isPlan) {
                $('.back-nav').show()
                $('.current').removeClass('current').hide().next().show().addClass('current');
                $('#progressbar li.active').next().addClass('active');
                $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                $('#progressbar li.active').prevAll('li').addClass('preve');
                $('#progressbar li.active').last().prev('li').removeClass('preve');
                 $(window).scrollTop(0);
            }
        });
        $(document).on('click', '.back-nav', function() {
            $("#errormsg").remove();
            $('.next-nav').show()
            if ($('.current').hasClass("field2")) {
                $('.back-nav').hide()
            }
            $('.current').removeClass('current').hide().prev().show().addClass('current');
            $('#progressbar li.active').removeClass('active').prev().addClass('active');
            $('#progressbar li.active').removeClass('hide-lable').prev().addClass('hide-lable');
            $('#progressbar li.active').last().prev('li').removeClass('preve');
        });*/

        $(document).on('click', '.edit-booking-address', function() {
            $('.next-nav').show()
            $('.back-nav').hide()
            $('.current').removeClass('current').hide()
            $('.field1').addClass('current').show()
            $('#progressbar li.active').removeClass('active')
            $('#progressbar li.hide-lable').removeClass('hide-lable')
            $('#progressbar li.preve').removeClass('preve')
            $('#progressbar li:first').addClass('active')
        });
        /*add wallet money*/
        $(document).on('click', '#wallet-select', function(){
            $("#errormsg").remove();
            let self = $(this);
            var ischecked= self.is(':checked');
            let applyTotal = {
                grandTotal: $("input[name='wallet']:checked").val()
            };
            if (ischecked && $("input[name='wallet']:checked").val() > 0){
                $.ajax({
                    url: urlBuilder.build('services/grooming/applywallet'),
                    method: "POST",
                    data: applyTotal,
                    showLoader: true,
                    success: function(response) {
                        if (response.success) {
                            $('<div class="message-success success message" id="errormsg">' + response.message + '</div>').insertAfter(self.parent());
                            setTimeout(function() {
                                renderReview('');
                            }, 600);
                        } else {
                            $('<div class="message-warning warning message" id="errormsg">' + response.message + '</div>').insertAfter(self.parent());
                        }
                    }
                });
            } else if (!ischecked) {
                $.ajax({
                    url: urlBuilder.build('services/grooming/removewallet'),
                    method: "POST",
                    data: applyTotal,
                    showLoader: true,
                    success: function(response) {
                        if (response.success) {
                            $('<div class="message-success success message" id="errormsg">' + response.message + '</div>').insertAfter(self.parent());
                            setTimeout(function() {
                                renderReview('');
                            }, 600);
                        } else {
                            $('<div class="message-warning warning message" id="errormsg">' + response.message + '</div>').insertAfter(self.parent());
                        }
                    }
                });
            }
        });
        $(document).on('click', '.btn-payment-make', function() {
            $("#errormsg").remove();
            let paymode = "";
            if ($("input[type='radio'][name='paymode']").length) {
                paymode = $("input[type='radio'][name='paymode']:checked").val()
                if (!paymode) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a payment.</div>').insertAfter('#progressbar');
                    return false;
                }
            }
            let placeData = {
                mode: paymode
            };

            $.ajax({
                url: urlBuilder.build('services/grooming/place'),
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
                                // "description": "Test Transaction",
                                // "image": "https://example.com/your_logo",
                                "order_id": response.razorData.razorId,
                                "callback_url": urlBuilder.build('services/grooming/paynow')+'/?id='+response.razorData.orderId,
                                "cancel_url" : urlBuilder.build('services/grooming'),
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
                            console.log(require('Magento_Customer/js/customer-data').reload(['customer']))
                        } else {
                            window.location.href = urlBuilder.build('services/grooming/success')
                        }
                    } else {
                        $('<div class="message-error error message groom" id="errormsg">' + response.message + '</div>').insertAfter('#progressbar');
                    }
                }
            });
        });
        $(document).on('click', '.booking-payment #apply-coupon', function() {
            $("#errormsg").remove();
            let self = $(this)
            let paymode = $("input[name='service-coupon']").val()
            if (!paymode) {
                $('<div class="message-error error message groom" id="errormsg">Invalid coupon code.</div>').insertAfter(self.parent());
                return false;
            }
            let applyData = {
                couponcode: $("input[name='service-coupon']").val()
            };
            $.ajax({
                url: urlBuilder.build('services/grooming/applycoupon'),
                method: "POST",
                data: applyData,
                showLoader: true,
                success: function(response) {
                    if (response.success) {
                        $('<div class="message-success success message" id="errormsg">' + response.message + '</div>').insertAfter(self.parent());
                        setTimeout(function() {
                            renderReview('');
                        }, 600);
                    } else {
                        $('<div class="message-warning warning message" id="errormsg">' + response.message + '</div>').insertAfter(self.parent());

                    }

                }
            });
        });

        $(document).on('click', '.booking-payment #remove-coupon', function() {

            $("#errormsg").remove();
            renderReview()
            let self = $(this)
            let paymode = $("input[name='service-coupon']").val()
            if (!paymode) {
                $('<div class="message-error error message groom" id="errormsg">Invalid coupon code.</div>').insertAfter(self);
                return false;
            }
            let applyData = {
                couponcode: $("input[name='service-coupon']").val()
            };
            $.ajax({
                url: urlBuilder.build('services/grooming/removecoupon'),
                method: "POST",
                data: applyData,
                showLoader: true,
                success: function(response) {
                    if (response.success) {
                        $('<div class="message-success success message" id="errormsg">' + response.message + '</div>').insertAfter(self.parent());
                        setTimeout(function() {
                            renderReview('');
                        }, 600);
                    } else {
                        $('<div class="message-warning warning message" id="errormsg">' + response.message + '</div>').insertAfter(self.parent());
                    }
                }
            });
        });

        function renderReview(postAddress) {
            $.ajax({
                url: urlBuilder.build('services/grooming/review'),
                method: "POST",
                data: postAddress,
                showLoader: true,
                success: function(response) {
                    $('.field5').html(response.output);
                    $(".selected-address-value").remove();
                    $(".selected-address").text("Selected Address");
                    $('<p class="step-value selected-address-value">'+response.address+'</p>').insertAfter(".selected-address");
                }
            });
        }

        /* Selections */
        $(document).on('click', '#plan-select-defined', function() {
            let self = $(this);
            $('input[name="plan_id"]').val(self.data("planid"))
            $('input[name="plan_is_custom"]').val('1')
            $('div.plan').removeClass('selected')
            $('.activity-check').prop('checked', false);
            $('div.plan[data-planid="' + self.data("planid") + '"]').addClass('selected')
            let postAddress = {
                planid: $('input[name="plan_id"]').val(),
                activity: ''
            };

            $.ajax({
                url: urlBuilder.build('services/grooming/setplan'),
                method: "POST",
                data: postAddress,
                showLoader: true,
                success: function(response) {
                    if (!response.success) {
                        $('.next-nav').hide()
                        $('<div class="message-error error message groom" id="errormsg">'+response.message+'</div>').insertAfter('#progressbar');
                        return false;
                    } else {
                        $(".selected-plan-value").remove();
                        $(".selected-plan").text("Selected Plan");
                        $('<p class="step-value selected-plan-value">'+response.plan_name+'</p>').insertAfter(".selected-plan");
                        $(".selected-time-value").remove();
                        $(".selected-address-value").remove();
                        $("input[name='select_detail']").val("");
                        $('.plan-booked').html(response.output);
                        $('.next-nav').show()
                        $('.current').removeClass('current').hide().next().show().addClass('current');
                        $('#progressbar li.active').next().addClass('active ');
                        $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                        $('#progressbar li.active').prevAll('li').addClass('preve');
                        $('#progressbar li.active').last().prev('li').removeClass('preve');
                         $(window).scrollTop(0);
                        $('.detail-options').hide();
                    }
                }
            })
        });
        $(document).on('click', '#plan-select-custom', function() {
            $("#errormsg").remove();
            let self = $(this);
            if (!self.parent().parent().find('input[type="checkbox"]:checked').length) {
                self.parent().append('<div class="message-error error message groom" id="errormsg">Please select activities.</div>')
                self.parent().parent().find('.detail-options').show()
                return false;
            }
            $('input[name="plan_id"]').val(self.data("planid"))
            $('input[name="plan_is_custom"]').val('2')
            $('div.plan').removeClass('selected')
            $('div.plan[data-planid="' + self.data("planid") + '"]').addClass('selected')
            let customActivity = [];
            $($('div.plan[data-planid="' + $('input[name="plan_id"]').val() + '"]').find('input[type="checkbox"]:checked')).each(function() {
                customActivity.push($(this).val());
            });

            let postAddress = {
                planid: $('input[name="plan_id"]').val(),
                activity: customActivity
            };
            $.ajax({
                url: urlBuilder.build('services/grooming/setplan'),
                method: "POST",
                data: postAddress,
                showLoader: true,
                success: function(response) {
                    if (!response.success) {
                        $('.next-nav').hide()
                        $('<div class="message-error error message groom" id="errormsg">'+response.message+'</div>').insertAfter('#progressbar');
                        return false;
                    } else {
                        $(".selected-plan-value").remove();
                        $(".selected-plan").text("Selected Plan");
                        $('<p class="step-value selected-plan-value">'+response.plan_name+'</p>').insertAfter(".selected-plan");
                        $(".selected-time-value").remove();
                        $(".selected-address-value").remove();
                        $("input[name='select_detail']").val("");
                        $('.plan-booked').html(response.output);
                        $('.next-nav').show()
                        $('.current').removeClass('current').hide().next().show().addClass('current');
                        $('#progressbar li.active').next().addClass('active ');
                        $('#progressbar li.active').prevAll('li').addClass('hide-lable');
                        $('#progressbar li.active').prevAll('li').addClass('preve');
                        $('#progressbar li.active').last().prev('li').removeClass('preve');
                         $(window).scrollTop(0);
                        $('.detail-options').hide();
                    }
                }
            });
        });
        $(document).on('change', '.activity-check', function() {
            let selectedprice = $(this).parent().find('.custom-plan-price').data("price")
            let selectedtime = $(this).parent().find('.custom-plan-duration').data("time")
            let priceElement = $(this).parent().parent().parent().parent().parent().parent().find('.plan-price .price')
            let priceElement2 = $(this).parent().parent().parent().parent().parent().parent().find('.total-price .custom-total-price strong')
            let timeElement = $(this).parent().parent().parent().parent().parent().parent().find('.duration .plan-duration')
            let timeElement2 = $(this).parent().parent().parent().parent().parent().parent().find('.total-price .custom-total-time')
            let currentprice = priceElement.data("estimatedprice")
            let currenttime = timeElement.data("estimatedmin")
            let updatedPrice = "--"
            let updatedTime = "--"
            if (this.checked) {
                updatedPrice = (+selectedprice) + (+currentprice)
                updatedTime = (+selectedtime) + (+currenttime)

            } else {
                updatedPrice = (+currentprice) - (+selectedprice)
                updatedTime = (+currenttime) - (+selectedtime)
            }

            // priceElement.html(priceUtils.formatPrice(updatedPrice, priceFormat))
            priceElement2.html(priceUtils.formatPrice(updatedPrice, priceFormat))
            priceElement.data("estimatedprice", updatedPrice)
            timeElement.html(updatedTime)
            timeElement2.html(updatedTime)
            timeElement.data("estimatedmin", updatedTime)
        });

        $(document).on('click', '#grooming-timeslot .slot-time', function() {
            var select_time = $('.selected-time');
            if(select_time) {
                $(".time-proceed").prop('disabled', false);
            }else{
                $(".time-proceed").prop('disabled', true);
            }
            $("#errormsg").remove();
            $("#grooming-timeslot .slot-time").removeClass('selected-time');
            if (!$('input[name="selected_date"]').val()) {
                $('<div class="message-error error message groom" id="errormsg">Please select a date before selecting time.</div>').insertAfter('#progressbar');
                return false;
            }
            $('input[name="selected_time"]').val($(this).data('slot'));
            $(this).addClass('selected-time');
            let postAddress = {
                selected_date: $('input[name="selected_date"]').val(),
                selected_time: $(this).data('slot')
            };
            $.ajax({
                url: urlBuilder.build('services/grooming/timeslot'),
                method: "POST",
                data: postAddress,
                showLoader: true,
                success: function(response) {}
            });
        });

        /* Address */
        var options = {
            type: 'slide',
            responsive: true,
            innerScroll: true,
            modalClass: 'add-address-modal',
            title: 'Add New Address',
            buttons: [],
            opened: function() {
                var addressFormUrl = urlBuilder.build('services/grooming/addressform');
                $.ajax({
                    url: addressFormUrl,
                    method: "POST",
                    showLoader: true,
                    success: function(response) {
                        $('#address-modal').html(response.output);
                        var statecookie = $.cookie("state");
                        var street1 = $.cookie("street1");
                        var street2 = $.cookie("street2");
                        var pincode = $.cookie("pincode_check");
                        var citycookie = $.cookie("city_screen");
                        if (statecookie) {
                            $('#street_1').val(street1)
                            $('#street_2').val(street2)
                            $('#city').val(citycookie)
                            $('#region_id').find('option:contains("' + statecookie + '")').attr('selected', 'selected');
                            $('#zip').val(pincode)
                        }
                        $('#city').val(citycookie);

                        let myLatlng = { lat: 28.70, lng: 77.10 };
                        if ($.cookie("glatlng")) {
                            let glatlng = JSON.parse($.cookie("glatlng"));
                            myLatlng = { lat: parseFloat(glatlng.lat), lng: parseFloat(glatlng.lng)};
                        }
                        const map = new google.maps.Map(document.getElementById("maps-container"), {
                            zoom: 12,
                            center: myLatlng,
                            streetViewControl: false,
                            mapTypeControl: false,
                            scaleControl: false,
                            scrollwheel: false,
                            fullscreenControl: false,
                            keyboardShortcuts: false,
                        });
                        var marker = new google.maps.Marker({
                            // position: myLatlng,
                            map: map
                        });
                        // Create the initial InfoWindow.
                        /*  let infoWindow = new google.maps.InfoWindow({
                            content: "Click the map to get Lat/Lng!",
                            position: myLatlng,
                          });
                          infoWindow.open(map);*/
                        // Configure the click listener.
                        map.addListener("click", (mapsMouseEvent) => {
                            var geocoder = new google.maps.Geocoder();
                            var latlng = mapsMouseEvent.latLng
                            geocoder.geocode({
                                'latLng': latlng
                            }, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    var arrAddress = results[0].address_components;
                                    var city = '';
                                    var state = '';
                                    var postCode = '';
                                    var Address1 = '';
                                    var Address2 = '';
                                    $.each(arrAddress, function(i, address_component) {
                                        for (var j = 0; j < address_component.types.length; j++) {
                                            if (address_component.types[j] == "street_number") {
                                                Address1 = address_component.long_name;
                                            } else if (address_component.types[j] == "route") {
                                                Address1 = Address1 + address_component.long_name;
                                            } else if (address_component.types[j] == "sublocality") {
                                                Address2 = address_component.long_name;
                                            } else if (address_component.types[j] == "locality") {
                                                city = address_component.long_name;
                                            } else if (address_component.types[j] == "administrative_area_level_1") {
                                                state = address_component.long_name;
                                            } else if (address_component.types[j] == "postal_code") {
                                                postCode = address_component.short_name;
                                            }
                                        }
                                    });
                                    $('#street_1').val(Address1)
                                    $('#street_2').val(Address2)
                                    $('#city').val(city)
                                    $('#region_id').find('option:contains("' + state + '")').attr('selected', 'selected');
                                    $('#zip').val(postCode)
                                }
                            });
                            // Close the current InfoWindow.
                            /*infoWindow.close();*/
                            // // Create a new InfoWindow.
                            marker.setPosition(mapsMouseEvent.latLng)
                            /*var marker = new google.maps.Marker({
                                position: mapsMouseEvent.latLng,
                                map: map
                            });*/
                            /*infoWindow = new google.maps.InfoWindow({
                              position: mapsMouseEvent.latLng,
                            });*/
                            // // infoWindow.setContent(
                            // //   JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)
                            // // );
                            /*infoWindow.open(map);*/
                        });
                    }
                });
            },
            closed: function() {
                $('#address-modal').html('');
            }
        };
        // var popup = modal(options, $('#address-modal'));
        $(document).on('click', '#openslider', function() {
            $("#address-modal").modal(options).modal("openModal");
        });
        var offeroptions = {
            type: 'popup',
            responsive: true,
            modalClass: 'show-offer-modal',
            title: 'Offers',
            buttons: []
        };
        $(document).on('click', ".show-offers", function() {
            $('#offers-modal').modal(offeroptions).modal('openModal');
        });
        var editoptions = {
            type: 'slide',
            responsive: true,
            innerScroll: true,
            modalClass: 'add-address-modal',
            title: 'Edit Address',
            buttons: [],
            closed: function() {
                $('#address-edit-modal').html('');
            }
        };
        // var editpopup = modal(options, $('#address-edit-modal'));
        $(document).on('click', '#edit-address', function() {
            var addressFormUrl = urlBuilder.build('services/grooming/addressform');
            let self = $(this)
            $.ajax({
                url: addressFormUrl,
                method: "POST",
                dataType: 'json',
                showLoader: true,
                data: {
                    "id": self.data('id')
                },
                success: function(response) {
                    $('#address-edit-modal').html(response.output);
                }
            });
            $("#address-edit-modal").modal(editoptions).modal("openModal");
        });
        // save-address
        $(document).on('click', '#save-service-address', function() {

            var customurl = urlBuilder.build('services/grooming/addresspost');
            $('#address-popup-form').validation();
            $("#errormsg").remove();
            if ($('#address-popup-form').validation('isValid')) {
                $.ajax({
                    url: customurl,
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    data: $('#address-popup-form').serialize(),
                    complete: function(response) {
                        if (!response.responseJSON.errors) {
                            $('<div class="message-success success message groom" id="errormsg">' + response.responseJSON.message + '</div>').insertAfter('#address-popup-form .actions-toolbar');
                            setTimeout(function() {
                                $(".add-address-modal .action-close").trigger("click")
                            }, 400);
                            renderAddress();
                        } else {
                            $('<div class="message-error error message groom" id="errormsg">' + response.responseJSON.message + '</div>').insertAfter('#address-popup-form .actions-toolbar');
                        }
                    },
                    error: function(xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    }
                });
            }
        });

        /*delete-address*/
        $(document).on('click', '#delete-address', function() {
            let self = $(this)
            var addressDeleteUrl = urlBuilder.build('services/grooming/deleteaddress');
            confirmation({
                content: $.mage.__('Are you sure you want to delete this address?'),
                actions: {
                    confirm: function() {
                        $.ajax({
                            url: addressDeleteUrl,
                            method: "POST",
                            dataType: 'json',
                            showLoader: true,
                            data: {
                                "id": self.data('address')
                            },
                            success: function(response) {
                                if (!response.error) {
                                    $('<div class="message-success success message groom" id="errormsg">' + response.message + '</div>').insertAfter('#progressbar');
                                    renderAddress();

                                } else {
                                    $('<div class="message-error error message groom" id="errormsg">' + response.message + '</div>').insertAfter('#progressbar');
                                }
                            }
                        });
                        setTimeout(function() {
                            $("#errormsg").remove();
                        }, 400);
                    },
                    cancel: function() {},
                    always: function() {}
                }
            });
        });

        function renderAddress() {
            var renderAddressUrl = urlBuilder.build('services/grooming/address');
            $.ajax({
                url: renderAddressUrl,
                method: "POST",
                showLoader: true,
                success: function(response) {
                    $('.field4 .address-list').html(response.output);
                    $(".selected-time-value").remove();
                    $("#progressbar li > .steps > .step-name > .selected-time").text("Selected Time Slot");
                    $('<p class="step-value selected-time-value">'+response.date+' | '+response.time+'</p>').insertAfter("#progressbar li > .steps > .step-name > .selected-time");
                }
            });
        }

        /* calendar */
        var startDate = new Date(config.serverTime.replace(/-/g, "/"));
        startDate.setDate(startDate.getDate() - 1)
        var endDate = new Date(config.serverTime.replace(/-/g, "/"));
        endDate.setDate(endDate.getDate() + 7)
        var calendarEl = document.getElementById('grooming-calendar-slot');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            // firstDay: 1,
            // firstDay: new Date().getDay(),
            // eventColor: '#d7f0f7',
            // showNonCurrentDates: false,
            eventDisplay: 'block',
            eventTextColor: "#000",

            // disableDragging: true,
            selectable: true,
            selectConstraint: {
                start: startDate,
                end: endDate
            },
            dateClick: function(info) {
                $("#errormsg").remove();
                $("#noticemsg").remove();
                $("#availmsg").remove();
                $(".fc-daygrid-day").removeClass('fc-day-today');
                $('input[name="selected_time"]').val('');
                var days = document.querySelectorAll(".selectedDate");
                var clickedDay = info.date.getTime();
                let today = new Date(config.serverTime.replace(/-/g, "/"))
                let timeToday = new Date(config.serverTime.replace(/-/g, "/"))
                let todayTime = new Date(config.serverTime.replace(/-/g, "/"))
                var from = startDate.getTime();
                var to = endDate.getTime();
                if (clickedDay >= from && clickedDay <= to) {
                    $(".time-proceed").prop('disabled', true);
                    days.forEach(function(day) {
                        day.classList.remove("selectedDate");
                    });
                    info.dayEl.classList.add("selectedDate");
                    var dateString = moment(info.date).format('YYYY-MM-DD');
                    $('input[name="selected_date"]').val(dateString);
                    var todayString = moment(today).format('YYYY-MM-DD');

                    
                    let availableTimesSlots = '';
                    if (!window.availableTimesSlotsHtml) {
                        window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
                    }
                    if(moment(today).minute()> 30){
                        moment(today).minute(30).second(0);
                    } else {
                        moment(today).minute(0).second(0);
                    }
                    if (todayString == dateString && parseInt(moment(today).format('H')) > (parseInt(config.startHr) - 2)) {
                        if (parseInt(moment(today).format('H')) < 23) {
                            var coeff = 1000 * 60 * 30;
                            today = new Date(Math.round(today.getTime() / coeff) * coeff)
                            timeToday = new Date(Math.round(today.getTime() / coeff) * coeff)
                        }
                        if(moment(today).minute() > 30){
                            today.setHours(today.getHours() + 1);
                        }
                        let $time = today;
                        let $timeToday = timeToday;
                        let currentHr = moment(today).format('H')
                        let currentMin = moment(today).format('mm')
                        today.setMinutes(today.getMinutes() + 120)
                        let firstSlotHr = moment(today).format('H')
                        let firstSlotMin = moment(today).format('mm')
                        var endHr = parseInt(config.endHr)
                        var endMin = parseInt(config.endMin)
                        let timeString = todayTime.setHours(config.startHr)
                        timeToday.setHours(timeToday.getHours() + 1)
                        var currentBeforeHr = timeToday.setMinutes(timeToday.getMinutes() + 30)
                        let currentTimeHr = moment(timeToday).format('h')
                        var currBeforeHr = moment(currentBeforeHr).format('h');
                        var currentTimeMin = moment(currentBeforeHr).format('h:mm a');
                        /*if (parseInt(currentHr) < 11) {
                            if (parseInt(currentMin) < 30) {
                                var currentTimeMin = moment(currentBeforeHr).add(1, 'hours').format('h')+':30 am';
                            } else {
                                var currentTimeMin = moment(currentBeforeHr).add(1, 'hours').format('h')+':00 am';
                            }
                        } else {
                            if (parseInt(currentMin) < 30) {
                                var currentTimeMin = moment(currentBeforeHr).add(1, 'hours').format('h')+':30 pm';
                            } else {
                                var currentTimeMin = moment(currentBeforeHr).add(1, 'hours').format('h')+':00 pm';
                            }
                        }*/
                        todayTime.setMinutes(config.startMin)
                        if (parseInt(config.startHr) < 23) {
                            var coeff = 1000 * 60 * 30;
                            timeString = new Date(Math.round(todayTime.getTime() / coeff) * coeff)
                        }
                        let $timeslot = timeString;
                        if ((parseInt(currentHr) == endHr && parseInt(currentMin) >= endMin) || parseInt(currentHr) > endHr) {
                            $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                            $("#grooming-timeslot ol").html('');
                        } else if ((parseInt(firstSlotHr) == endHr && parseInt(firstSlotMin) > endMin) || parseInt(firstSlotHr) > endHr) {
                            $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                            $("#grooming-timeslot ol").html('');
                        } else {
                            while ($timeslot) {
                                let $prevTime = new Date($timeslot);
                                let $prevs = moment($prevTime).format('h:mm a');
                                let $nexts = $prevTime.setMinutes($prevTime.getMinutes() + 30 );
                                $timeslot = $nexts;
                                availableTimesSlots += '<li class="non-slot-time">'+$prevs+'</li>';
                                if ($prevs == currentTimeMin) {
                                    break;
                                }
                            }
                            while ($time) {
                                let $prevDate = new Date($time);
                                let $prev = moment($prevDate).format('h:mm a');
                                let $next = $prevDate.setMinutes($prevDate.getMinutes() + 30 );
                                $time = $next;
                                availableTimesSlots += '<li class="slot-time" data-slot="'+$prev+'">'+$prev+'</li>';
                                if ($prev == (config.endTime)) {
                                    break;
                                }
                            }
                            $("#grooming-timeslot ol").html(availableTimesSlots);
                            var numItems = $('.slot-time').length
                            if (numItems == 2) {
                                $('<div class="slot-msg" id="availmsg">Only 2 slots left.</div>').insertAfter('.full');
                            } else if (numItems == 1) {
                                $('<div class="slot-msg" id="availmsg">Only 1 slot left.</div>').insertAfter('.full');
                            } else if (numItems == 0) {
                                $('<div class="slot-msg" id="availmsg">No Available slots.</div>').insertAfter('.full');
                            }
                        }
                    } else {
                        if (window.availableTimesSlotsHtml) {
                            $("#grooming-timeslot ol").html(window.availableTimesSlotsHtml);
                        }
                    }
                }
            },
            events: {},
        });
        calendar.render();        

        if (!window.availableTimesSlotsHtml) {
            /*window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
            $('<div class="message-notice notice message" id="noticemsg">Please select a date before selecting time.</div>').insertAfter('#grooming-timeslot ol');
            $("#grooming-timeslot ol").html('');*/
            let today = new Date(config.serverTime.replace(/-/g, "/"))
            let timeToday = new Date(config.serverTime.replace(/-/g, "/"))
            let todayTime = new Date(config.serverTime.replace(/-/g, "/"))
            var from = startDate.getTime();
            var to = endDate.getTime();
            var dateString = moment(new Date()).format('YYYY-MM-DD');
            console.log(dateString);
            $('input[name="selected_date"]').val(dateString);
            var todayString = moment(today).format('YYYY-MM-DD');
            let availableTimesSlots = '';
            if (!window.availableTimesSlotsHtml) {
                window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
            }
            if(moment(today).minute()> 30){
                moment(today).minute(30).second(0);
            } else {
                moment(today).minute(0).second(0);
            }
            if (todayString == dateString && parseInt(moment(today).format('H')) > (parseInt(config.startHr) - 2)) {
                if (parseInt(moment(today).format('H')) < 23) {
                    var coeff = 1000 * 60 * 30;
                    today = new Date(Math.round(today.getTime() / coeff) * coeff)
                    timeToday = new Date(Math.round(today.getTime() / coeff) * coeff)
                }
                if(moment(today).minute() > 30){
                    today.setHours(today.getHours() + 1);
                }
                let $time = today;
                let $timeToday = timeToday;
                let currentHr = moment(today).format('H')
                let currentMin = moment(today).format('mm')
                today.setMinutes(today.getMinutes() + 120)
                let firstSlotHr = moment(today).format('H')
                let firstSlotMin = moment(today).format('mm')
                var endHr = parseInt(config.endHr)
                var endMin = parseInt(config.endMin)
                let timeString = todayTime.setHours(config.startHr)
                timeToday.setHours(timeToday.getHours() + 1)
                var currentBeforeHr = timeToday.setMinutes(timeToday.getMinutes() + 30)
                let currentTimeHr = moment(timeToday).format('h')
                var currBeforeHr = moment(currentBeforeHr).format('h');
                var currentTimeMin = moment(currentBeforeHr).format('h:mm a');
                todayTime.setMinutes(config.startMin)
                if (parseInt(config.startHr) < 23) {
                    var coeff = 1000 * 60 * 30;
                    timeString = new Date(Math.round(todayTime.getTime() / coeff) * coeff)
                }
                let $timeslot = timeString;
                if ((parseInt(currentHr) == endHr && parseInt(currentMin) >= endMin) || parseInt(currentHr) > endHr) {
                    $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                    $("#grooming-timeslot ol").html('');
                } else if ((parseInt(firstSlotHr) == endHr && parseInt(firstSlotMin) > endMin) || parseInt(firstSlotHr) > endHr) {
                    $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                    $("#grooming-timeslot ol").html('');
                } else {
                    while ($timeslot) {
                        let $prevTime = new Date($timeslot);
                        let $prevs = moment($prevTime).format('h:mm a');
                        let $nexts = $prevTime.setMinutes($prevTime.getMinutes() + 30 );
                        $timeslot = $nexts;
                        availableTimesSlots += '<li class="non-slot-time">'+$prevs+'</li>';
                        if ($prevs == currentTimeMin) {
                            break;
                        }
                    }
                    while ($time) {
                        let $prevDate = new Date($time);
                        let $prev = moment($prevDate).format('h:mm a');
                        let $next = $prevDate.setMinutes($prevDate.getMinutes() + 30 );
                        $time = $next;
                        availableTimesSlots += '<li class="slot-time" data-slot="'+$prev+'">'+$prev+'</li>';
                        if ($prev == (config.endTime)) {
                            break;
                        }
                    }
                    $("#grooming-timeslot ol").html(availableTimesSlots);
                    var numItems = $('.slot-time').length
                    if (numItems == 2) {
                        $('<div class="slot-msg" id="availmsg">Only 2 slots left.</div>').insertAfter('.full');
                    } else if (numItems == 1) {
                        $('<div class="slot-msg" id="availmsg">Only 1 slot left.</div>').insertAfter('.full');
                    } else if (numItems == 0) {
                        $('<div class="slot-msg" id="availmsg">No Available slots.</div>').insertAfter('.full');
                    }
                }
            } else {
                if (window.availableTimesSlotsHtml) {
                    $("#grooming-timeslot ol").html(window.availableTimesSlotsHtml);
                }
            }
        }
    }
});