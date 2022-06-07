/**
 * Copyright (C) 2020  Zigly
 * @package Zigly_Login
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'mage/validation',
    'jquery/ui',
    'mage/translate'
], function($, modal, urlBuilder) {
    'use strict';

    return function(config) {

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            showLoader: true,
            modalClass: 'verify-phone-modal',
            title: 'Verify Phone Number',
            buttons: [{
                text: $.mage.__('Verify'),
                class: 'verify-otp',
                click: function (data) {
                    $("#errormsg").remove();
                    var phone = $("input[name='phone_number']").val();
                    if (phone == '') {
                        phone = $("#phone_number").val();
                    }
                    let verifyData = {
                        username: phone,
                        otp: $("input[name='verify_otp']").val()
                    };
                    $.ajax({
                        url: urlBuilder.build('login/verify/verifyotp'),
                        type: 'POST',
                        showLoader: true,
                        data : verifyData,
                        complete: function(response){
                            if (response.responseJSON.status == 0) {
                                $('#verify-modal').modal(this).modal('openModal');
                                $('<div class="message-error error message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('.otp-verify-form');
                                $('#edit-customer-profile, #create-account-action').hide();
                                $('.phone-verify').show();
                            } else {
                                $('#verify-modal').modal(this).modal('closeModal');
                                $('<div class="message-success success message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('#form-validate');
                                $('#edit-customer-profile, #create-account-action').show();
                                $('.phone-verify').hide();
                            }
                        },
                        error: function (xhr, status, errorThrown) {
                            console.log('Error happens. Try again.');
                        }
                    });
                }
            }]
        };
        $(document).ready(function (){
            $('.phone-verify').hide();
            $('.email-verify').hide();
        });
        $(document).on('click', ".verify-otp, #resend-otp, #resend-otp-email",function(){
            $("#errormsg").remove();
        });
        /*verify phone number start*/
        $(document).on('change', "input[name='phone_number']",function(){
            $("#errormsg").remove();
            var phone = $(this).val();
            if ($.isNumeric(phone) && phone.length == 10 && phone != config.oldphonenumber) {
                $('#edit-customer-profile, #create-account-action').hide();
                $('.phone-verify').show();
            } else {
                $('#edit-customer-profile, #create-account-action').show();
                $('.phone-verify').hide();
            }
        });
        $(document).on('click', ".verify-phone", function() { // phone number popup model
            $("#errormsg").remove();
            $("input[name='verify_otp']").val("")
            var oldemail = config.oldemail;
            var oldphonenumber = config.oldphonenumber;
            var phone = $("input[name='phone_number']").val();
            if (phone == '') {
                phone = $("#phone_number").val();
            }
            let formData = {
                username: phone,
                oldemail: oldemail,
                oldphonenumber: oldphonenumber
            };
            $.ajax({
                url: urlBuilder.build('login/verify/sendotp'),
                method: "POST",
                showLoader: true,
                data: formData,
                complete: function(response) {
                    if (response.responseJSON.status == 0) {
                        $('<div class="message-error error message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('#form-validate');
                    } else {
                        $('#verify-modal').modal(options).modal('openModal');
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        });
        $(document).on('click', "#resend-otp", function() { // resend popup model
            $("input[name='verify_otp']").val('')
            $("#errormsg").remove();
            var oldemail = config.oldemail;
            var oldphonenumber = config.oldphonenumber;
            var phone = $("input[name='phone_number']").val();
            if (phone == '') {
                phone = $("#phone_number").val();
            }
            let resendData = {
                username: phone,
                oldemail: oldemail,
                oldphonenumber: oldphonenumber
            };
            $.ajax({
                url: urlBuilder.build('login/verify/resendotp'),
                method: "POST",
                showLoader: true,
                data: resendData,
                complete: function(response) {
                    if (response.responseJSON.status == 0) {
                        $('#verify-modal').modal(this).modal('openModal');
                        $('<div class="message-error error message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('.otp-verify-form');
                        $('#edit-customer-profile, #create-account-action').hide();
                        $('.phone-verify').show();
                    } else {
                        $('#verify-modal').modal(this).modal('openModal');
                        $('<div class="message-success success message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('.otp-verify-form');
                        $('#edit-customer-profile, #create-account-action').show();
                        $('.phone-verify').hide();
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        });
        /*verify phone number end*/
        /*verify email start*/
        var emailOptions = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            showLoader: true,
            modalClass: 'verify-email-modal',
            title: 'Verify Email',
            buttons: [{
                text: $.mage.__('Verify'),
                class: 'verify-otp',
                click: function (data) {
                    $("#errormsg").remove();
                    var email = $("input[name='email']").val();
                    if (email == '') {
                        email = $("#email_address").val();
                    }
                    let verifyData = {
                        username: email,
                        otp: $("input[name='verify_otp_email']").val()
                    };
                    $.ajax({
                        url: urlBuilder.build('login/verify/verifyotp'),
                        type: 'POST',
                        showLoader: true,
                        data : verifyData,
                        complete: function(response){
                            if (response.responseJSON.status == 0) {
                                $('#verify-modal-email').modal(this).modal('openModal');
                                $('<div class="message-error error message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('.otp-verify-form');
                                $('#edit-customer-profile, #create-account-action').hide();
                                $('.email-verify').show();
                            } else {
                                $('#verify-modal-email').modal(this).modal('closeModal');
                                $('<div class="message-success success message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('#form-validate');
                                $('#edit-customer-profile, #create-account-action').show();
                                $('.email-verify').hide();
                            }
                        },
                        error: function (xhr, status, errorThrown) {
                            console.log('Error happens. Try again.');
                        }
                    });
                }
            }]
        };
        $(document).on('change', "input[name='email']",function(){
            $("#errormsg").remove();
            var email = $(this).val();
            var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(!regex.test(email)) {
                $('.email-verify').hide();
                $('<div class="message-error error message groom" id="errormsg">Please enter a valid email address (Ex: johndoe@domain.com).</div>').insertAfter('.email-verify-check');
                return false;
            } else {
                if (email != config.oldemail) {
                    $('#edit-customer-profile, #create-account-action').hide();
                    $('.email-verify').show();
                } else {
                    $('#edit-customer-profile, #create-account-action').show();
                    $('.email-verify').hide();
                }
            }
        });
        $(document).on('click', ".verify-email", function() { // email popup model
            $("input[name='verify_otp_email']").val()
            $("#errormsg").remove();
            var oldemail = config.oldemail;
            var oldphonenumber = config.oldphonenumber;
            var email = $("input[name='email']").val();
            if (email == '') {
                email = $("#email_address").val();
            }
            let emailData = {
                username: email,
                oldemail: oldemail,
                oldphonenumber: oldphonenumber
            };
            $.ajax({
                url: urlBuilder.build('login/verify/sendotp'),
                method: "POST",
                showLoader: true,
                data: emailData,
                complete: function(response) {
                    if (response.responseJSON.status == 0) {
                        $('<div class="message-error error message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('#form-validate');
                    } else {
                        $('#verify-modal-email').modal(emailOptions).modal('openModal');
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        });
        $(document).on('click', "#resend-otp-email", function() { // resend popup model
            $("input[name='verify_otp_email']").val('')
            $("#errormsg").remove();
            var oldemail = config.oldemail;
            var oldphonenumber = config.oldphonenumber;
            var email = $("input[name='email']").val();
            if (email == '') {
                email = $("#email_address").val();
            }
            let resendData = {
                username: email,
                oldemail: oldemail,
                oldphonenumber: oldphonenumber
            };
            $.ajax({
                url: urlBuilder.build('login/verify/resendotp'),
                method: "POST",
                showLoader: true,
                data: resendData,
                complete: function(response) {
                    if (response.responseJSON.status == 0) {
                        $('#verify-modal-email').modal(this).modal('openModal');
                        $('<div class="message-error error message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('.otp-verify-form');
                        $('#edit-customer-profile, #create-account-action').hide();
                        $('.email-verify').show();
                    } else {
                        $('#verify-modal-email').modal(this).modal('openModal');
                        $('<div class="message-success success message groom" id="errormsg">'+response.responseJSON.msg+'.</div>').insertBefore('.otp-verify-form');
                        $('#edit-customer-profile, #create-account-action').show();
                        $('.email-verify').hide();
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        });
        /*verify email end*/
    }
});