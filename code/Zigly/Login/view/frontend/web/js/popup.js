/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */

define(
    [
        'jquery',
        'Magento_Customer/js/customer-data',
        'mage/translate',
        'Magento_Ui/js/modal/modal',
        'mage/url',
        'mage/cookies',
        'Mageplaza_Core/js/jquery.magnific-popup.min'
    ], function ($, customerData, $t, modal, urlBuilder) {
        'use strict';

        $.widget(
            'mageplaza.socialpopup', {
                options: {
                    /*General*/
                    popup: '#social-login-popup',
                    popupEffect: '',
                    headerLink: '.header .links, .section-item-content .header.links',
                    ajaxLoading: '#social-login-popup .ajax-loading',
                    loadingClass: 'social-login-ajax-loading',
                    errorMsgClass: 'message-error error message',
                    successMsgClass: 'message-success success message',
                    /*Login*/
                    loginFormContainer: '.social-login.authentication',
                    loginFormContent: '.social-login.authentication .social-login-customer-authentication .block-content',
                    loginForm: '#social-form-login',
                    loginBtn: '#bnt-social-login-authentication',
                    forgotBtn: '#social-form-login .action.remind',
                    createBtn: '#social-form-login .action.create',
                    formLoginUrl: '',
                    /*Email*/
                    emailFormContainer: '.social-login.fake-email',
                    fakeEmailSendBtn: '#social-form-fake-email .action.send',
                    fakeEmailType: '',
                    fakeEmailFrom: '#social-form-fake-email',
                    fakeEmailFormContent: '.social-login.fake-email .block-content',
                    fakeEmailUrl: '',
                    fakeEmailCancelBtn: '#social-form-fake-email .action.cancel',
                    /*Forgot*/
                    forgotFormContainer: '.social-login.forgot',
                    forgotFormContent: '.social-login.forgot .block-content',
                    forgotForm: '#social-form-password-forget',
                    forgotSendBtn: '#social-form-password-forget .action.send',
                    forgotBackBtn: '#social-form-password-forget .action.back',
                    forgotFormUrl: '',
                    /*Create*/
                    createFormContainer: '.social-login.create',
                    createFormContent: '.social-login.create .block-content',
                    createForm: '#social-form-create',
                    createAccBtn: '#social-form-create .action.create',
                    createBackBtn: '#social-form-create .action.back',
                    createFormUrl: '',
                    defaultCreateFormContainer: '#form-validate',
                    defaultCreateFormContent: '#form-validate',
                    defaultCreateForm: '#form-validate',
                    defaultCreateAccBtn: '#form-validate #create-account-action',
                    showFields: '',
                    availableFields: ['name', 'email', 'password'],
                    condition: false,
                    popupLogin: false,
                    actionName: '',
                    firstName: '',
                    lastName: ','
                },

                /**
                 * @private
                 */
                _create: function () {
                    var self = this;
                    this.initObject();
                    this.initLink();
                    this.initObserve();
                    this.replaceAuthModal();
                    this.hideFieldOnPopup();
                    window.fakeEmailCallback = function (type, firstname, lastname) {
                        self.options.fakeEmailType = type;
                        self.options.firstName     = firstname;
                        self.options.lastName      = lastname;
                        self.showEmail();
                    };
                },

                /**
                 * Init object will be used
                 */
                initObject: function () {
                    this.loginForm  = $(this.options.loginForm);
                    this.createForm = $(this.options.createForm);
                    this.defaultCreateForm = $(this.options.defaultCreateForm);
                    this.forgotForm = $(this.options.forgotForm);

                    this.forgotFormContainer = $(this.options.forgotFormContainer);
                    this.createFormContainer = $(this.options.createFormContainer);
                    this.defaultCreateFormContainer = $(this.options.defaultCreateFormContainer);
                    this.loginFormContainer  = $(this.options.loginFormContainer);

                    this.loginFormContent  = $(this.options.loginFormContent);
                    this.forgotFormContent = $(this.options.forgotFormContent);
                    this.createFormContent = $(this.options.createFormContent);
                    this.defaultCreateFormContent = $(this.options.defaultCreateFormContent);

                    this.emailFormContainer   = $(this.options.emailFormContainer);
                    this.fakeEmailFrom        = $(this.options.fakeEmailFrom);
                    this.fakeEmailFormContent = $(this.options.fakeEmailFormContent);
                },

                /**
                 * Init links login
                 */
                initLink: function () {
                    var self       = this,
                        headerLink = $(this.options.headerLink);

                    if (headerLink.length && self.options.popupLogin) {
                        headerLink.find('a').each(
                            function (link) {
                                var el   = $(this),
                                    href = el.attr('href');

                                if (typeof href !== 'undefined' && (href.search('customer/account/login') !== -1 || href.search('customer/account/create') !== -1)) {
                                    self.addAttribute(el);
                                    el.on(
                                        'click', function (event) {
                                            if (href.search('customer/account/create') !== -1) {
                                                self.showCreate();
                                            } else {
                                                self.showLogin();
                                            }

                                            event.preventDefault();
                                        }
                                    );
                                }
                            }
                        );
                        if (self.options.popupLogin === 'popup_login') {
                            self.enablePopup(headerLink, 'a.social-login-btn');
                        }
                    }

                    this.options.createFormUrl = this.correctUrlProtocol(this.options.createFormUrl);
                    this.options.formLoginUrl  = this.correctUrlProtocol(this.options.formLoginUrl);
                    this.options.forgotFormUrl = this.correctUrlProtocol(this.options.forgotFormUrl);
                    this.options.fakeEmailUrl  = this.correctUrlProtocol(this.options.fakeEmailUrl);
                
                    console.log(this.options.createFormUrl);
                },

                /**
                 * Correct url protocol to match with current protocol
                 *
                 * @param   url
                 * @returns {*}
                 */
                correctUrlProtocol: function (url) {
                    var protocol = window.location.protocol;
                    if (!url.includes(protocol)) {
                        url = url.replace(/http:|https:/gi, protocol);
                    }

                    return url;
                },

                /**
                 * Init button click
                 */
                initObserve: function () {
                    this.initLoginObserve();
                    this.initCreateObserve();
                    this.initDefaultCreateObserve();
                    this.initForgotObserve();
                    this.initEmailObserve();

                    $(this.options.createBtn).on('click', this.showCreate.bind(this));
                    $(this.options.forgotBtn).on('click', this.showForgot.bind(this));
                    $(this.options.createBackBtn).on('click', this.showLogin.bind(this));
                    $(this.options.forgotBackBtn).on('click', this.showLogin.bind(this));
                },

                /**
                 * Login process
                 */
                initLoginObserve: function () {
                    var self = this;

                    $(this.options.loginBtn).on('click', this.processLogin.bind(this));
                    var current_fs, next_fs, previous_fs;
                    var left, opacity, scale;
                    var animating;
                    self.removeMsg(self.loginFormContent, self.options.errorMsgClass);
                    $(document).on("click","#resendotp",function() {
                        self.removeMsg(self.loginFormContainer, self.options.errorMsgClass);
                        if($('.otp-timmer').html() == ''){
                        var baseurl = self.options.formLoginUrl.replace("customer/ajax/login/", "");
                        var sendotpmailurl = baseurl+"login/otp/resendotp";
                        $.ajax({
                            url: sendotpmailurl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                username: $('input[name="username"]').val()
                            },
                            complete: function(response) {
                                if(response.responseJSON.status == 0){
                                    var jsonString = {
                                            message: response.responseJSON.msg,
                                            success: false
                                        };
                                    self.addMsg(self.loginFormContainer, jsonString);
                                    //setTimeout(function(){ self.removeMsg(self.loginFormContainer, self.options.errorMsgClass); }, 2000);
                                }else{
                                    $('#resendotp').css('cursor','default');
                                    var timer2 = "0:20";
                                    var interval = setInterval(function() {
                                      var timer = timer2.split(':');
                                      //by parsing integer, I avoid all extra string processing
                                      var minutes = parseInt(timer[0], 10);
                                      var seconds = parseInt(timer[1], 10);
                                      --seconds;
                                      minutes = (seconds < 0) ? --minutes : minutes;
                                      if (minutes < 0) clearInterval(interval);
                                      seconds = (seconds < 0) ? 59 : seconds;
                                      seconds = (seconds < 10) ? '0' + seconds : seconds;
                                      //minutes = (minutes < 10) ?  minutes : minutes;
                                      if(seconds == '00' || minutes == '-1'){
                                        $('.otp-timmer').html('');
                                        $('#resendotp').css('cursor','pointer');
                                      }else{
                                        $('.otp-timmer').html(minutes + ':' + seconds);
                                      }
                                      timer2 = minutes + ':' + seconds;
                                    }, 1000);
                                }
                            },
                            error: function (xhr, status, errorThrown) {
                                console.log('Error happens. Try again.');
                            }
                        });
                        }
                    });
                    $(document).on("click","#bnt-auth-mobile",function() {
                        
                        self.removeMsg(self.loginFormContent, self.options.errorMsgClass);
                        var selfagain = $(this);
                        $('input[name="username"]').validation();
                        if(!$('input[name="username"]').validation('isValid')){
                            return false;
                        }
                        $('input[name="login_pass_1"]').val('')
                        $('input[name="login_pass_2"]').val('')
                        $('input[name="login_pass_3"]').val('')
                        $('input[name="login_pass_4"]').val('')
                        $('.mfp-close.close1').addClass('step2')
                        current_fs = $(this).parent();
                        next_fs = $(this).parent().next();
                        if(animating) return false;
                        animating = true;
                        next_fs.find('.mobile-num').html($('input[name="username"]').val());
                        next_fs.show();
                        current_fs.animate({opacity: 0}, {
                            step: function(now, mx) {
                                scale = 1 - (1 - now) * 0.2;
                                left = (now * 50)+"%";
                                opacity = 1 - now;
                                current_fs.css({
                            'transform': 'scale('+scale+')'
                            // 'position': 'absolute'
                            });
                                next_fs.css({'left': left, 'opacity': opacity});

                            },
                            duration: 600,
                            complete: function(){
                                current_fs.hide();
                                animating = false;
                            },
                            //this comes from the custom easing plugin
                            easing: 'easeInOutBack'
                        });
                        var baseurl = self.options.formLoginUrl.replace("customer/ajax/login/", "");
                        var sendotpmailurl = baseurl+"login/otp/sendotp";
                        $.ajax({
                            url: sendotpmailurl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                username: $('input[name="username"]').val()
                            },
                            complete: function(response) {
                                //if(response.responseJSON.otp != 0){
                                   //setTimeout(function(){
                                    $('.resendotpblock').show();
                                    $('#resendotp').css('cursor','default');
                                        var timer2 = "0:20";
                                        var interval = setInterval(function() {
                                          var timer = timer2.split(':');
                                          //by parsing integer, I avoid all extra string processing
                                          var minutes = parseInt(timer[0], 10);
                                          var seconds = parseInt(timer[1], 10);
                                          --seconds;
                                          minutes = (seconds < 0) ? --minutes : minutes;
                                          if (minutes < 0) clearInterval(interval);
                                          seconds = (seconds < 0) ? 59 : seconds;
                                          seconds = (seconds < 10) ? '0' + seconds : seconds;
                                          //minutes = (minutes < 10) ?  minutes : minutes;
                                          if(seconds == '00' || minutes == '-1'){
                                            $('.otp-timmer').html('');
                                            $('#resendotp').css('cursor','pointer');
                                          }else{
                                            $('.otp-timmer').html(minutes + ':' + seconds);
                                          }
                                          timer2 = minutes + ':' + seconds;
                                        }, 1000);
                                    //}, 1000);
                                //}
                            },
                            error: function (xhr, status, errorThrown) {
                                console.log('Error happens. Try again.');
                            }
                        });
                    });
                    $(document).on("click",".switch-to-email",function(e) {
                        e.preventDefault();
                        window.ziglyphonenumberHtml = $('input[name="username"]')[0].outerHTML
                        $('input[name="username"]').replaceWith('<input name="username" id="social_login_email" type="email" class="input-text" value="" autocomplete="off" title="Email Address" data-validate="{required:true, \'validate-email\':true}" placeholder="Enter Your Registered Email Id" aria-required="true">')
                        $('input[name="username"]').parent().parent().prepend('<div class="login-mail-title">Login Via Email</div>');
                        $('.message-error').remove()
                        $('span.username-prefix').html('')
                        $('span.username-prefix').addClass('mail')
                        $('.acknowledgemsg .message').html('We have sent to you an access code via Mail to ')
                        $('.acknowledgemsg .mobile-num-pre').remove('')
                        $('input[name="password"]').val('')
                        $('input[name="login_pass_1"]').val('')
                        $('input[name="login_pass_2"]').val('')
                        $('input[name="login_pass_3"]').val('')
                        $('input[name="login_pass_4"]').val('')
                        $('.social-login-authentication-channel').hide()
                        current_fs = $(this).parent().parent();
                        previous_fs = $(this).parent().parent().prev();
                        if(animating) return false;
                        animating = true;
                        previous_fs.show();
                        current_fs.animate({opacity: 0}, {
                            step: function(now, mx) {
                                //as the opacity of current_fs reduces to 0 - stored in "now"
                                //1. scale previous_fs from 80% to 100%
                                scale = 0.8 + (1 - now) * 0.2;
                                //2. take current_fs to the right(50%) - from 0%
                                left = ((1-now) * 50)+"%";
                                //3. increase opacity of previous_fs to 1 as it moves in
                                opacity = 1 - now;
                                current_fs.css({'left': left});
                                previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
                            },
                            duration: 600,
                            complete: function(){
                                current_fs.hide();
                                animating = false;
                            },
                            easing: 'easeInOutBack'
                        });
                        // $(this).parent().remove();
                        // $(".switch-email").replaceWith();
                        // $('.social-login-authentication-channel').after('<div class="actions-switch username"><span>Cannot access email?</span><a class="action switch-to-email" href="#"><span>Use mobile no. to login</span></a></div>');
                        $('.actions-toolbar.username').show()
                        $('.actions-toolbar.switcher span').html('Cannot access email?')
                        $('.actions-toolbar.switcher a').html('Use mobile no. to login')
                        $('.actions-toolbar.switcher a').removeClass('switch-to-email')
                        $('.actions-toolbar.switcher a').addClass('switch-to-number')

                    });
                    $(document).on("click",".switch-email",function(e) {
                        e.preventDefault();
                        window.ziglyphonenumberHtml = $('input[name="username"]')[0].outerHTML
                        $('input[name="username"]').replaceWith('<input name="username" id="social_login_email" type="email" class="input-text" value="" autocomplete="off" title="Email Address" data-validate="{required:true, \'validate-email\':true}" placeholder="Enter Your Registered Email Id" aria-required="true">')
                        $('input[name="username"]').parent().parent().prepend('<div class="login-mail-title">Login Via Email</div>');
                        $('#social_login_email-error').remove()
                        $('.message-error').remove()
                        $('span.username-prefix').html('')
                        $('span.username-prefix').addClass('mail')
                        $('.acknowledgemsg .message').html('We have sent to you an access code via Mail to ')
                        $('.acknowledgemsg .mobile-num-pre').remove('')
                        $('.social-login-authentication-channel').hide()
                        current_fs = $(this).parent().parent();
                        previous_fs = $(this).parent().parent().prev();
                        if(animating) return false;
                        animating = true;
                        current_fs.animate({opacity: 0}, {
                            step: function(now, mx) {
                                scale = 0.8 + (1 - now) * 0.2;
                                left = ((1-now) * 50)+"%";
                                opacity = 1 - now;
                                current_fs.css({'left': left}); //, 'opacity': opacity
                                current_fs.css({'transform': 'scale('+scale+')'});
                            },
                            duration: 600,
                            complete: function(){
                                current_fs.css({'opacity': 1});
                                animating = false;
                            },
                            easing: 'easeInOutBack'
                        });
                        // $(this).parent().remove();
                        // $(".switch-to-email").parent().remove();
                        $('.actions-toolbar.username').show()
                        $('.actions-toolbar.switcher span').html('Cannot access email?')
                        $('.actions-toolbar.switcher a').html('Use mobile no. to login')
                        $('.actions-toolbar.switcher a').removeClass('switch-to-email')
                        $('.actions-toolbar.switcher a').addClass('switch-to-number')

                    });
                    $(document).on("click",".switch-to-number",function(e) {
                        e.preventDefault();
                        // window.ziglyemailnumberHtml = $('input[name="username"]')[0].outerHTML
                        if (window.ziglyphonenumberHtml) {
                            $('input[name="username"]').replaceWith(window.ziglyphonenumberHtml)
                            $('.login-mail-title').remove()
                            $('#social_login_email-error').remove()
                            $('.message-error').remove()
                            $('span.username-prefix').html('+91')
                            $('span.username-prefix').removeClass('mail')
                            $('.acknowledgemsg .message').html('A verification code has been sent to your number via SMS to ')
                            $('.acknowledgemsg .mobile-num-pre').remove('+91')
                            $('.actions-toolbar.username').hide()
                            $('.actions-toolbar.switcher span').html('Cannot access mobile no?')
                            $('.actions-toolbar.switcher a').html('Use email to login')
                            $('.actions-toolbar.switcher a').addClass('switch-to-email')
                            $('.actions-toolbar.switcher a').removeClass('switch-to-number')

                        }
                        $('.fieldset.login').removeAttr("style")
                        $('.fieldset.login').eq(0).show()
                        $('.fieldset.login').eq(1).hide()

                        $('input[name="password"]').val('')
                        $('input[name="login_pass_1"]').val('')
                        $('input[name="login_pass_2"]').val('')
                        $('input[name="login_pass_3"]').val('')
                        $('input[name="login_pass_4"]').val('')
                        $('.social-login-authentication-channel').show()

                        // $('input[name="username"]').replaceWith('<input name="username" id="social_login_email" type="email" class="input-text" value="" autocomplete="off" title="Mobile Number" data-validate="{required:true, \'validate-email\':true}" placeholder="Enter Your Registered Email Id" aria-required="true">')
                        // $('.login-mail-title').remove();
                        current_fs = $(this).parent().parent();
                        previous_fs = $(this).parent().parent().prev();
                        if(animating) return false;
                        animating = true;
                        current_fs.animate({opacity: 0}, {
                            step: function(now, mx) {
                                scale = 0.8 + (1 - now) * 0.2;
                                left = ((1-now) * 50)+"%";
                                opacity = 1 - now;
                                current_fs.css({'left': left}); //, 'opacity': opacity
                                current_fs.css({'transform': 'scale('+scale+')'});
                            },
                            duration: 600,
                            complete: function(){
                                current_fs.css({'opacity': 1});
                                animating = false;
                            },
                            easing: 'easeInOutBack'
                        });
                        // $(this).parent().remove();
                        // $(".switch-to-email").parent().remove();
                    });
                    (function($) {
                      $.fn.inputFilter = function(inputFilter) {
                        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
                          if (inputFilter(this.value)) {
                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                          } else if (this.hasOwnProperty("oldValue")) {
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                          } else {
                            this.value = "";
                          }
                        });
                      };
                    }(jQuery));
                    $(document).on("click", 'input[name="username"][type="tel"]',function() {
                        $('input[name="username"][type="tel"]').inputFilter(function(value) {
                            return /^\d*$/.test(value);    // Allow digits only, using a RegExp
                        });
                    });

                    this.loginForm.find('input[name="username"]').keypress(
                        function (event) {
                            var code = event.keyCode || event.which;
                            if (code === 13) {
                                $("#bnt-auth-mobile").trigger('click')
                            }
                        }
                    );
                    this.loginForm.find('input[type="password"]').on("input",function (e) {
                        var target = event.srcElement || event.target;
                        var maxLength = parseInt(target.attributes["maxlength"].value, 10);
                        var myLength = target.value.length;
                        if (myLength >= maxLength) {
                            var next = target;
                            while (next = next.nextElementSibling) {
                                if (next == null)
                                    break;
                                if (next.getAttribute("name").includes('login_pass')) {
                                    next.focus();
                                    break;
                                }
                            }
                        }
                     });
                    this.loginForm.find('input[type="password"]').keypress(
                        function (event) {
                            if (event.which < 48 || event.which > 57) {
                                event.preventDefault();
                            }
                            var code = event.keyCode || event.which;
                            if (code === 13) {
                                self.processLogin();
                            }
                        }
                    );
                    this.loginForm.find('input[type="password"]').keydown(
                        function (event) {
                            var code = event.keyCode || event.which;
                            if (code == 8) {
                                event.preventDefault();
                                var target = event.srcElement || event.target;
                                var prev = target;
                                target.value = '';
                                while (prev = prev.previousElementSibling) {
                                    if (prev == null)
                                        break;
                                    if (prev.getAttribute("name").includes('login_pass')) {
                                        prev.focus();
                                        break;
                                    }
                                }
                            }
                        }
                    );
                },

                /**
                 * Create process
                 */
                initCreateObserve: function () {
                    var self = this;

                    $(this.options.createAccBtn).on('click', this.processCreate.bind(this));
                    this.createForm.find('input').keypress(
                        function (event) {
                            var code = event.keyCode || event.which;
                            if (code === 13) {
                                self.processCreate();
                            }
                        }
                    );
                },

                /**
                 *  Default Create process
                 */
                initDefaultCreateObserve: function () {
                    var self = this;

                    $(this.options.defaultCreateAccBtn).on('click', this.processDefaultCreate.bind(this));
                        this.defaultCreateForm.find('input').keypress(
                        function (event) {
                            var code = event.keyCode || event.which;
                            if (code === 13) {
                                self.processDefaultCreate();
                            }
                        }
                    );
                },

                /**
                 * Forgot process
                 */
                initForgotObserve: function () {
                    var self = this;

                    $(this.options.forgotSendBtn).on('click', this.processForgot.bind(this));
                    this.forgotForm.find('input').keypress(
                        function (event) {
                            var code = event.keyCode || event.which;
                            if (code === 13) {
                                self.processForgot();
                            }
                        }
                    );
                },

                /**
                 * Email process
                 */
                initEmailObserve: function () {
                    var self = this;

                    $(this.options.fakeEmailSendBtn).on('click', this.processEmail.bind(this));
                    this.fakeEmailFrom.find('input').keypress(
                        function (event) {
                            var code = event.keyCode || event.which;
                            if (code === 13) {
                                self.processEmail();
                            }
                        }
                    );
                },

                /**
                 * Show Login page
                 */
                showLogin: function () {
                    this.reloadCaptcha('login', 50);
                    this.loginFormContainer.show();
                    this.forgotFormContainer.hide();
                    this.createFormContainer.hide();
                    this.emailFormContainer.hide();
                },

                /**
                 * Show email page
                 */
                showEmail: function () {
                    var wrapper = $('#social-login-popup'),
                        actions = ['customer_account_login', 'customer_account_create', 'multishipping_checkout_login'];

                    if (this.options.popupLogin !== 'popup_login') {
                        if (this.options.popupLogin === 'popup_slide') {
                            $('.quick-login-wrapper').modal('closeModal');
                        }
                        var options = {
                            'type': 'popup',
                            'responsive': true,
                            'modalClass': 'request-popup',
                            'buttons': [],
                            'parentModalClass': '_has-modal request-popup-has-modal'
                        };
                        modal(options, wrapper);
                        wrapper.modal('openModal');
                    }

                    if ($.inArray(this.options.actionName, actions) !== -1) {
                        this.options.popupLogin ? $('.social-login-btn').trigger('click') : wrapper.modal('openModal');
                        this.emailFormContainer.show();
                    }

                    $('#request-firstname').val(this.options.firstName);
                    $('#request-lastname').val(this.options.lastName);
                    this.emailFormContainer.show();
                    this.loginFormContainer.hide();
                    this.forgotFormContainer.hide();
                    this.createFormContainer.hide();
                },

                /**
                 * Open Modal
                 */
                openModal: function () {
                },

                /**
                 * Show create page
                 */
                showCreate: function () {
                    this.reloadCaptcha('create', 50);
                    this.loginFormContainer.hide();
                    this.forgotFormContainer.hide();
                    this.createFormContainer.show();
                    this.emailFormContainer.hide();
                },

                /**
                 * Show forgot password page
                 */
                showForgot: function () {
                    this.reloadCaptcha('forgot', 50);
                    this.loginFormContainer.hide();
                    this.forgotFormContainer.show();
                    this.createFormContainer.hide();
                    this.emailFormContainer.hide();
                },

                /**
                 * Reload captcha if enabled
                 *
                 * @param type
                 * @param delay
                 */
                reloadCaptcha: function (type, delay) {
                    if (typeof this.captchaReload === 'undefined') {
                        this.captchaReload = {
                            all: $('#social-login-popup .captcha-reload'),
                            login: $('#social-login-popup .authentication .captcha-reload'),
                            create: $('#social-login-popup .create .captcha-reload'),
                            forgot: $('#social-login-popup .forgot .captcha-reload')
                        };
                    }

                    if (typeof type === 'undefined') {
                        type = 'all';
                    }

                    if (this.captchaReload.hasOwnProperty(type) && this.captchaReload[type].length) {
                        if (typeof delay === 'undefined') {
                            this.captchaReload[type].trigger('click');
                        } else {
                            var self = this;
                            setTimeout(
                                function () {
                                    self.captchaReload[type].trigger('click');
                                }, delay
                            );
                        }
                    }
                },

                /**
                 * Process login
                 */
                processLogin: function () {
                    $('input[name="password"]').val(
                            $('input[name="login_pass_1"]').val() +
                            $('input[name="login_pass_2"]').val() +
                            $('input[name="login_pass_3"]').val() +
                            $('input[name="login_pass_4"]').val()
                        )
                    if (!this.loginForm.valid()) {
                        return;
                    }
                    var self          = this,
                        options       = this.options,
                        loginData     = {},
                        formDataArray = this.loginForm.serializeArray();

                    formDataArray.forEach(
                        function (entry) {
                            loginData[entry.name] = entry.value;
                            if (entry.name.includes('user_login')) {
                                loginData['captcha_string']  = entry.value;
                                loginData['captcha_form_id'] = 'user_login';
                            }
                        }
                    );

                    this.appendLoading(this.loginFormContent);
                    this.removeMsg(this.loginFormContent, options.errorMsgClass);

                    return $.ajax(
                        {
                            url: options.formLoginUrl,
                            type: 'POST',
                            data: JSON.stringify(loginData)
                        }
                    ).done(
                        function (response) {
                            response.success = !response.errors;
                            if (response.message == "user-not-exists") {
                                response.message = "Please continue to create an account."
                                response.success = true;
                                self.addMsg(self.loginFormContent, response);
                                var url = urlBuilder.build("customer/account/create/");
                                let username = $('input[name="username"]').val()
                                let usernameType = $('input[name="username"]').attr('type')
                                var newForm = $('<form>', {
                                    'action': url,
                                    'method': 'post',
                                    'target': '_top'
                                }).append($('<input>', {
                                    'name': usernameType+"val",
                                    'value': username,
                                    'type': 'hidden'
                                }));
                                // if($('input[name="referralcode"]').val()) {
                                //     let referralcode = $('input[name="referralcode"]').val();
                                //     newForm.append($('<input>', {
                                //         'name': "referralcode",
                                //         'value': referralcode,
                                //         'type': 'hidden'
                                //     }));
                                // }
                                $(newForm).appendTo('body');
                                $(newForm).submit();
                                return;
                            } else {
                                self.addMsg(self.loginFormContent, response);
                            }
                            if (response.success) {
                                customerData.invalidate(['customer']);
                                if ($.cookie('afterloginurl')) {
                                    window.location.href = $.cookie('afterloginurl');
                                    $.cookie("afterloginurl", null);
                                } else if (response.redirectUrl) {
                                    window.location.href = response.redirectUrl;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                self.reloadCaptcha('login');
                                self.removeLoading(self.loginFormContent);
                            }
                        }
                    ).fail(
                        function () {
                            self.reloadCaptcha('login');
                            self.addMsg(
                                self.loginFormContent, {
                                    message: $t('Could not authenticate. Please try again later'),
                                    success: false
                                }
                            );
                            self.removeLoading(self.loginFormContent);
                        }
                    );
                },

                /**
                 * Process forgot
                 */
                processForgot: function () {
                    if (!this.forgotForm.valid()) {
                        return;
                    }

                    var self       = this,
                        options    = this.options,
                        parameters = this.forgotForm.serialize();

                    this.appendLoading(this.forgotFormContent);
                    this.removeMsg(this.forgotFormContent, options.errorMsgClass);
                    this.removeMsg(this.forgotFormContent, options.successMsgClass);

                    return $.ajax(
                        {
                            url: options.forgotFormUrl,
                            type: 'POST',
                            data: parameters
                        }
                    ).done(
                        function (response) {
                            self.reloadCaptcha('forgot');
                            self.addMsg(self.forgotFormContent, response);
                            self.removeLoading(self.forgotFormContent);
                        }
                    );
                },

                /**
                 * Process email
                 */
                processEmail: function () {
                    if (!this.fakeEmailFrom.valid()) {
                        return;
                    }
                    var input = $("<input>")
                    .attr("type", "hidden")
                    .attr("name", "type").val(this.options.fakeEmailType.toLowerCase());
                    $(this.fakeEmailFrom).append($(input));

                    var self       = this;
                    var options    = this.options,
                        parameters = this.fakeEmailFrom.serialize();

                    this.appendLoading(this.fakeEmailFormContent);
                    this.removeMsg(this.fakeEmailFormContent, options.errorMsgClass);
                    this.removeMsg(this.fakeEmailFormContent, options.successMsgClass);

                    return $.ajax(
                        {
                            url: options.fakeEmailUrl,
                            type: 'POST',
                            data: parameters
                        }
                    ).done(
                        function (response) {
                            self.addMsg(self.fakeEmailFrom, response);
                            self.removeLoading(self.fakeEmailFormContent);
                            if (response.success) {
                                if (response.url === '' || response.url == null) {
                                    window.location.reload(true);
                                } else {
                                    window.location.href = response.url;
                                }
                            }
                        }
                    );
                },

                /**
                 * Process create account
                 */
                processCreate: function () {
                    if (!this.createForm.valid()) {
                        return;
                    }

                    var self       = this,
                        options    = this.options,
                        parameters = this.createForm.serialize();

                    this.appendLoading(this.createFormContent);
                    this.removeMsg(this.createFormContent, options.errorMsgClass);

                    return $.ajax(
                        {
                            url: options.createFormUrl,
                            type: 'POST',
                            data: parameters
                        }
                    ).done(
                        function (response) {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else if (response.success) {
                                customerData.invalidate(['customer']);
                                self.addMsg(self.createFormContent, response);
                                if (response.url === '' || response.url == null) {
                                    window.location.reload(true);
                                } else {
                                    window.location.href = response.url;
                                }
                            } else {
                                self.reloadCaptcha('create');
                                self.addMsg(self.createFormContent, response);
                                self.removeLoading(self.createFormContent);
                            }
                        }
                    );
                },

                /**
                 * Process default create account
                 */
                processDefaultCreate: function () {
                    if (!this.defaultCreateForm.valid()) {
                        return;
                    }

                    var self       = this,
                        options    = this.options,
                        parameters = this.defaultCreateForm.serialize();

                    this.appendLoading(this.defaultCreateFormContent);
                    this.removeMsg(this.defaultCreateFormContent, options.errorMsgClass);

                    return $.ajax(
                        {
                            url: options.createFormUrl,
                            type: 'POST',
                            data: parameters
                        }
                    ).done(
                        function (response) {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else if (response.success) {
                                customerData.invalidate(['customer']);
                                self.addMsg(self.defaultCreateFormContent, response);
                                if (response.url === '' || response.url == null) {
                                    window.location.reload(true);
                                } else {
                                    window.location.href = response.url;
                                }
                            } else {
                                self.reloadCaptcha('create');
                                self.addMsg(self.defaultCreateFormContent, response);
                                self.removeLoading(self.defaultCreateFormContent);
                            }
                        }
                    );
                },

                /**
                 * @param block
                 */
                appendLoading: function (block) {
                    block.css('position', 'relative');
                    block.prepend($("<div></div>", {"class": this.options.loadingClass}))
                },

                /**
                 * @param block
                 */
                removeLoading: function (block) {
                    block.css('position', '');
                    block.find("." + this.options.loadingClass).remove();
                },

                /**
                 * @param block
                 * @param response
                 */
                addMsg: function (block, response) {
                    $('.social-login-customer-authentication .message-error.error.message').html('')
                    var message      = response.message,
                        messageClass = response.success ? this.options.successMsgClass : this.options.errorMsgClass;

                    if (typeof (message) === 'object' && message.length > 0) {
                        message.forEach(
                            function (msg) {
                                this._appendMessage(block, msg, messageClass);
                            }.bind(this)
                        );
                    } else if (typeof (message) === 'string') {
                        this._appendMessage(block, message, messageClass);
                    }
                },

                /**
                 * @param block
                 * @param messageClass
                 */
                removeMsg: function (block, messageClass) {
                    block.find('.' + messageClass.replace(/ /g, '.')).remove();
                },

                /**
                 * @param   block
                 * @param   message
                 * @param   messageClass
                 * @private
                 */
                _appendMessage: function (block, message, messageClass) {
                    var currentMessage = null;
                    var messageSection = block.find("." + messageClass.replace(/ /g, '.'));
                    if (!messageSection.length) {
                        block.prepend($('<div></div>', {'class': messageClass}));
                        currentMessage = block.children().first();
                    } else {
                        currentMessage = messageSection.first();
                    }

                    currentMessage.append($('<div>' + message + '</div>'));
                },

                /**
                 * Replace Authentication Popup with SL popup
                 */
                replaceAuthModal: function () {
                    var self           = this,
                        cartSummary    = $('.cart-summary'),
                        child_selector = 'button.social-login-btn',
                        cart           = customerData.get('cart'),
                        customer       = customerData.get('customer'),
                        miniCartBtn    = $('#minicart-content-wrapper'),
                        pccBtn         = $('button[data-role = proceed-to-checkout]');

                    var existCondition = setInterval(
                        function () {
                            if ($('#minicart-content-wrapper #top-cart-btn-checkout').length) {
                                clearInterval(existCondition);
                                if (!customer().firstname && cart().isGuestCheckoutAllowed === false && cart().isReplaceAuthModal) {
                                    self.options.condition = true;
                                }
                                self.addAttribute($('#minicart-content-wrapper #top-cart-btn-checkout'));
                                $('#minicart-content-wrapper').on(
                                    'click', ' #top-cart-btn-checkout', function (event) {
                                        if (self.options.condition) {
                                            self.openModal();
                                            self.showLogin();
                                            event.stopPropagation();
                                        }
                                    }
                                );
                                if (self.options.condition && self.options.popupLogin === 'popup_login') {
                                    self.enablePopup(miniCartBtn, child_selector);
                                }
                            }
                        }, 100
                    );

                    if (!customer().firstname && cart().isGuestCheckoutAllowed === false && cart().isReplaceAuthModal && pccBtn.length) {
                        pccBtn.replaceWith(
                            '<a title="Proceed to Checkout" class="action primary checkout social-login-btn">' +
                            '<span>Proceed to Checkout</span>' +
                            '</a>'
                        );
                        if (self.options.popupLogin === 'popup_login') {
                            self.addAttribute($('a.checkout.social-login-btn'));
                            self.enablePopup(cartSummary, 'a.social-login-btn');
                        }
                    }
                },

                /**
                 * Add attribute to element
                 *
                 * @param element
                 */
                addAttribute: function (element) {
                    var self = this;
                    element.addClass('social-login-btn');
                    element.attr('href', self.options.popup);
                    element.attr('data-effect', self.options.popupEffect);
                },

                /**
                 *  Enable Magnific Popup
                 *
                 * @param parent_selector
                 * @param child_selector
                 */
                enablePopup: function (parent_selector = null, child_selector = null) {
                    let self = this;
                    parent_selector.magnificPopup(
                        {
                            delegate: child_selector,
                            removalDelay: 500,
                            callbacks: {
                                beforeOpen: function () {
                                    this.st.mainClass = this.st.el.attr('data-effect');
                                },
                                afterClose: function() {
                                    if ($("body").hasClass("customer-account-login")) {
                                        var homeurl = urlBuilder.build("");
                                        window.location.href = homeurl
                                    }
                                    self.removeMsg(self.loginFormContainer, self.options.errorMsgClass);
                                },
                                open: function() {
                                  $.magnificPopup.instance.close = function() {
                                    if (window.ziglyphonenumberHtml) {
                                        $('input[name="username"]').replaceWith(window.ziglyphonenumberHtml)
                                        $('.login-mail-title').remove()
                                        $('span.username-prefix').html('+91')
                                        $('span.username-prefix').removeClass('mail')
                                        $('.acknowledgemsg .message').html('A verification code has been sent to your number via SMS to ')
                                        $('.acknowledgemsg .mobile-num-pre').remove('+91')
                                    }
                                    $('.fieldset.login').removeAttr("style")
                                    $('.fieldset.login').eq(0).show()
                                    $('.fieldset.login').eq(1).hide()

                                    $('input[name="password"]').val('')
                                    $('input[name="login_pass_1"]').val('')
                                    $('input[name="login_pass_2"]').val('')
                                    $('input[name="login_pass_3"]').val('')
                                    $('input[name="login_pass_4"]').val('')
                                    $('.social-login-authentication-channel').show()
                                    $('.actions-toolbar.username').hide()
                                    $('.social-login-customer-authentication #social_login_email-error').remove()
                                    
                                    $('.mfp-close.close1').removeClass('step2')
                                    $.magnificPopup.proto.close.call(this);
                                  };
                                }
                            },
                            midClick: true
                        }
                    );
                },

                /**
                 * function hide field not allow show on require more information popup
                 * */
                hideFieldOnPopup: function () {
                    var self = this;
                    $.each(
                        self.options.availableFields, function (k, fieldName) {
                            var elField   = $('.field-' + fieldName + '-social'),
                                elConfirm = $('.field-confirmation-social');
                            if (self.options.showFields) {
                                if ($.inArray(fieldName, self.options.showFields.split(',')) === -1) {
                                    if (fieldName === 'password') {
                                        elConfirm.remove();
                                    }
                                    elField.remove();
                                } else {
                                    elField.show();
                                }
                            }
                        }
                    );
                }
            }
        );

        return $.mageplaza.socialpopup;
    }
);
