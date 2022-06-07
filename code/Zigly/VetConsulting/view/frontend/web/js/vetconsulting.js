/**
 * Copyright (C) 2020  Zigly
 * @package Zigly_VetConsulting
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
    'jquery/ui',
    'jquery/jquery.cookie',
    'mage/translate',
    "domReady!"
], function($, modal, priceUtils, moment, urlBuilder, confirmation) {
    'use strict';

    return function(config) {

        $(document).ready(function(){
            var url = window.location.href;
            var step = url.split('?').pop().split('=').pop();
            if (step && step == "2" || step == "2/") {
                $.ajax({
                    url: config.getpets,
                    method: "POST",
                    showLoader: true,
                    success: function(response) {
                        if (response.status) {
                            $('.field2').html(response.output);
                            $('.next-nav').show()
                            $('.back-nav').show()
                            $('.current').removeClass('current').hide().next().show().addClass('current');
                            $('#progressbar li.active').next().addClass('active');
                        } else {
                            $('<div class="message-notice notice message" id="noticemsg">'+response.message+'</div>').insertAfter('#progressbar');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.check-specialty, .check-sort', function() {
            var formData = $("form").serialize();
            $.ajax({
                url: config.getvetbyfilter,
                showLoader: true,
                data: formData,
                success: function(response) {
                    $('.field5').html(response.output);
                    var nextElement = $('button.btn').next('div.filter-list');
                    nextElement.toggle();
                    if (!$(".check-specialty:checked").val()) {
                        $('.filter-contanier > .filter-list').css("display","none")
                    }
                    if (!$(".check-sort:checked").val()) {
                        $('.sort-contanier > .filter-list').css("display","none")
                    }
                    $('.next-nav').show()
                    $('.back-nav').show()
                }
            });
         });

        $(document).on('click', '.tab-a', function() {
            $(".tab").removeClass('tab-active');
            $(".tab[data-id='"+$(this).attr('data-id')+"']").addClass("tab-active");
            $(".tab-a").removeClass('active-a');
            $(this).parent().find(".tab-a").addClass('active-a');
         });

        $(document).on('click', '.view-vet-profile', function() {
            var key = $(this).attr('key');
            var popup = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                modalClass: 'vet-profile-modal',
                title: '',
                buttons: [],
                opened: function() {
                    var profileUrl = config.profileUrl;
                    $.ajax({
                        url: profileUrl,
                        method: "POST",
                        showLoader: true,
                        data: {'key': key},
                        success: function(response) {
                            $('.professional-profile-modal').html(response.output);
                        }
                    });
                },
                closed: function() {
                    $('.professional-profile-modal').html('');
                }
            };
            $(".professional-profile-modal").modal(popup).modal("openModal");
        });

        $(document).on('click', 'button.btn', function () {
            var nextElement = $(this).next('div.filter-list');
            return nextElement.toggle();
        });
        /*image upload start*/
        $(document).on('click', '#image-doc', function () {
            $("#imageDocUpload").click();
        });
        $(document).on('click', '.remove', function(){
            $(this).parent(".pip").parent('.image-pip').remove();
        });
        $(document).on('change', '#imageDocUpload', function(e) {
            $("#errormsg").remove();
            var files = e.target.files,
            filesLength = files.length,
            self = this;
            if(filesLength == 0) return true;
            var  data = new FormData();
            for (var index = 0; index < filesLength; index++) {
                var fileSize = self.files[index].size;
                var size = 1024000;
                if (!(fileSize <= size)) {
                    $('<div class="message-error error message groom" id="errormsg">The file size should not exceed 5MB.</div>').insertAfter('#progressbar');
                    return false;
                }
                data.append("image[]", self.files[index]);
            }
            /*data.append('image',files[0]);*/
            var uploadurl = config.uploadImageUrl;
            $.ajax({
                url: uploadurl,
                type: 'POST',
                contentType: false,
                processData: false,
                showLoader: true,
                data: data,
                complete: function(response) {
                    console.log(response.responseJSON);
                    let data = response.responseJSON
                    $.each( data, function( key, value ) {
                        $("#errormsg").remove();
                        if(value.error == 0){
                            $(self).val('');
                            if(value.type == 'image') {
                                $("<div class=\"image-pip\"><span class=\"pip\">" +
                                "<input type=\"hidden\" class=\"img-doc-uploaded\" accept=\"image/jpeg, image/png, image/jpg,\" name=\"" + $(self).attr('name') + "\" value=\"" + value.file + "\">"+
                                "<img class=\"imageThumb\" src=\"" + value.url + "\" title=\"" + value.name + "\"/>" +
                                "<span class=\"remove\">x</span></span></div>").insertAfter("#empty-painpoints");
                            }else{
                                $("<div class=\"image-pip\"><span class=\"pip\">" +
                                "<input type=\"hidden\" class=\"img-doc-uploaded\" accept=\"image/jpeg, image/png, image/jpg,\" name=\"" + $(self).attr('name') + "\" value=\"" + value.file + "\">"+value.name+
                                "<span class=\"remove\">x</span></span></div>").insertAfter("#empty-painpoints");
                            }
                        } else {
                            $('<div class="message-error error message groom" id="errormsg">'+response.responseJSON.error+'.</div>').insertAfter('#progressbar');
                        }
                    });
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        });
        /*image upload end*/
        /* Navigation */
        $(document).on('click', '.next-nav', function() {
            $("#errormsg").remove();
            $("#noticemsg").remove();
            if ($('.current').hasClass("field1")) { //location
                $.ajax({
                    url: config.getpets,
                    method: "POST",
                    showLoader: true,
                    success: function(response) {
                        if (response.status) {
                            $('.field2').html(response.output);
                            $('.next-nav').show()
                            $('.back-nav').show()
                            $('.current').removeClass('current').hide().next().show().addClass('current');
                            $('#progressbar li.active').next().addClass('active');
                            $(window).scrollTop(0);
                        } else {
                            $('<div class="message-notice notice message" id="noticemsg">'+response.message+'</div>').insertAfter('#progressbar');
                        }
                    }
                });
            } else if ($('.current').hasClass("field2")) { //pet
                let petId = $("input[type='radio'][name='select_pet']:checked").val();
                if (!petId) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a pet to continue.</div>').insertAfter('#progressbar');
                    return false;
                }
                let postPet = {
                    petid: petId
                };
                $.ajax({
                    url: config.getpainpoints,
                    method: "POST",
                    showLoader: true,
                    data: postPet,
                    success: function(response) {
                        $('.field3').html(response.output);
                        $('.next-nav').show()
                        $('.back-nav').show()
                        $('.current').removeClass('current').hide().next().show().addClass('current');
                        $('#progressbar li.active').next().addClass('active');
                        $(window).scrollTop(0);
                    }
                });
            } else if ($('.current').hasClass("field3")) { //pain points
                let painPoints = $('.check-painpoints:checked').map(function(_, el) {
                                    return $(el).val();
                                }).get();
                let description = $('#describe_problem').val();
                var vetImg = [];
                $("input:hidden.img-doc-uploaded").each(function() {
                   vetImg.push($(this).val());
                });
                let imgUploaded = vetImg.join();
                let postPainPoints = {
                    painPoints: painPoints,
                    description: description,
                    imgUploaded: imgUploaded
                };
                $.ajax({
                    url: config.settimeslot,
                    method: "POST",
                    showLoader: true,
                    data: postPainPoints,
                    success: function(response) {
                        if (!response.success) {
                            console.log('plan-select-NEXR')
                            $('.next-nav').hide()
                            $('<div class="message-error error message groom" id="errormsg">'+response.message+'</div>').insertAfter('#progressbar');
                            return false;
                        } else {
                            $('.next-nav').show()
                            $('.back-nav').show()
                            $('.current').removeClass('current').hide().next().show().addClass('current');
                            $('#progressbar li.active').next().addClass('active');
                        }
                    }
                });
            } else if ($('.current').hasClass("field4")) { //timeslot
                if (!$('input[name="selected_date"]').val() || !$('input[name="selected_time"]').val()) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a date and time.</div>').insertAfter('#progressbar');
                    return false;
                }
                $.ajax({
                    url: config.getvet,
                    showLoader: true,
                    success: function(response) {
                        $('.field5').html(response.output);
                        $('input[name="vet_id"]').val("")
                        $('.next-nav').show()
                        $('.back-nav').show()
                        $('.current').removeClass('current').hide().next().show().addClass('current');
                        $('#progressbar li.active').next().addClass('active');
                        $(window).scrollTop(0);
                    }
                });
            } else if ($('.current').hasClass("field5")) { //vet book appointment
                if (!$('input[name="vet_id"]').val()) {
                    $('<div class="message-error error message groom" id="errormsg">Please select a vet.</div>').insertAfter('#progressbar');
                    return false;
                }
                let postVet = {
                    vet_id: $('input[name="vet_id"]').val()
                };
                $.ajax({
                    url: config.reviewVet,
                    method: "POST",
                    data: postVet,
                    showLoader: true,
                    success: function(response) {
                        $('.field6').html(response.output);
                        $('.next-nav').hide()
                        $('.current').removeClass('current').hide().next().show().addClass('current');
                        $('#progressbar li.active').next().addClass('active');
                        $(window).scrollTop(0);
                    }
                });
            }
        });
        $(document).on('click', '.btn-book-appointment', function() { //vet book appointment
            $("#errormsg").remove();
            let self = $(this);
            $('input[name="vet_id"]').val(self.data("vet_id"))
            var schedule_id = '';
            if (self.data("schedule_id")) {
                schedule_id = self.data("schedule_id")
            }
            let postVet = {
                vet_id: $('input[name="vet_id"]').val(),
                schedule_id: schedule_id
            };
            $.ajax({
                url: config.reviewVet,
                method: "POST",
                data: postVet,
                showLoader: true,
                success: function(response) {
                    $('.field6').html(response.output);
                    $('.next-nav').hide()
                    $('.current').removeClass('current').hide().next().show().addClass('current');
                    $('#progressbar li.active').next().addClass('active');
                    $(window).scrollTop(0);
                }
            });
        });
        $(document).on('click', '.back-nav', function() { //back button
            $("#errormsg").remove();
            $('.next-nav').show()
            if ($('.current').hasClass("field2")) {
                $('.back-nav').hide()
            }
            $('.current').removeClass('current').hide().prev().show().addClass('current');
            $('#progressbar li.active').removeClass('active').prev().addClass('active');
        });

        $(document).on('click', '.edit-vet-booking', function() { // edit vet booking redirect to step one
            $('.next-nav').show()
            $('.back-nav').hide()
            $('.current').removeClass('current').hide()
            $('.field1').addClass('current').show()
            $('#progressbar li.active').removeClass('active')
            $('#progressbar li:first').addClass('active')
        });
        var offeroptions = {
            type: 'popup',
            responsive: true,
            modalClass: 'show-offer-modal',
            title: 'Offers',
            buttons: []
        };
        $(document).on('click', ".show-offers", function() { // offer popup model
            $('#offers-modal').modal(offeroptions).modal('openModal');
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
                    url: config.applyWallet,
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
                    url: config.removeWallet,
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
            } else {
                paymode = "no-pay";
            }
            let placeData = {
                mode: paymode
            };
            $.ajax({
                url: urlBuilder.build('services/vet/place'),
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
                                "callback_url": urlBuilder.build('services/vet/paynow')+'/?id='+response.razorData.orderId,
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
                            window.location.href = urlBuilder.build('services/vet/success')
                        }
                        console.log(require('Magento_Customer/js/customer-data').reload(['customer']))
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
                url: config.applyCoupon,
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
                url: config.removeCoupon,
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
                url: config.loadreview,
                method: "POST",
                data: postAddress,
                showLoader: true,
                success: function(response) {
                    $('.field6').html(response.output);
                    $('.next-nav').hide()
                }
            });
        }

        $(document).on('click', '#grooming-timeslot .slot-time', function() {
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
                url: config.timeslot,
                method: "POST",
                data: postAddress,
                showLoader: true,
                success: function(response) {}
            });
        });

        /* calendar */
        var startDate = new Date(config.serverTime.replace(/-/g, "/"));
        startDate.setDate(startDate.getDate() - 1)
        var endDate = new Date(config.serverTime.replace(/-/g, "/"));
        endDate.setDate(endDate.getDate() + 7)
        var calendarEl = document.getElementById('grooming-calendar-slot');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
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
                var days = document.querySelectorAll(".selectedDate");
                var clickedDay = info.date.getTime();
                let today = new Date(config.serverTime.replace(/-/g, "/"))
                var from = startDate.getTime();
                var to = endDate.getTime();
                if (clickedDay >= from && clickedDay <= to) {
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
                    if (todayString == dateString && parseInt(moment(today).format('H')) > (parseInt(config.startHr) - 2)) {
                        if (parseInt(moment(today).format('H')) < 23) {
                            var coeff = 1000 * 60 * 30;
                            today = new Date(Math.round(today.getTime() / coeff) * coeff)
                        }
                        let $time = today;
                        let currentHr = moment(today).format('H')
                        let currentMin = moment(today).format('mm')
                        today.setMinutes(today.getMinutes() + 120)
                        let firstSlotHr = moment(today).format('H')
                        let firstSlotMin = moment(today).format('mm')
                        var endHr = parseInt(config.endHr)
                        var endMin = parseInt(config.endMin)
                        if ((parseInt(currentHr) == endHr && parseInt(currentMin) >= endMin) || parseInt(currentHr) > endHr) {
                            $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                            $("#grooming-timeslot ol").html('');
                        } else if ((parseInt(firstSlotHr) == endHr && parseInt(firstSlotMin) >= endMin) || parseInt(firstSlotHr) > endHr) {
                            $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                            $("#grooming-timeslot ol").html('');
                        } else {
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
            window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
            $('<div class="message-notice notice message" id="noticemsg">Please select a date before selecting time.</div>').insertAfter('#grooming-timeslot ol');
            $("#grooming-timeslot ol").html('');
        }

        function initializeMapAutoComplete() {
            var mapAutoCompleteInput = document.getElementById('area_landmark');
            var options = {
                componentRestrictions: {country: "in"}
            };
            var autocomplete = new google.maps.places.Autocomplete(mapAutoCompleteInput, options);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                $("#noticemsg").remove();
                var place = autocomplete.getPlace();
                if (place.geometry.location.lng()){
                    var arrAddress = place.address_components;
                    var city ='';

                    $.each(arrAddress, function (i, address_component) {
                        for (var j = 0; j < address_component.types.length; j++) {
                            if (address_component.types[j] == "locality"){
                                city = address_component.long_name;
                                return false;
                            }
                        }
                    });
                    let postData = {
                        city: city,
                        place: place.formatted_address
                    };
                    $.ajax({
                        url: urlBuilder.build('services/vet/detectclinic'),
                        method:"POST",
                        data: postData,
                        showLoader: true,
                        success:function(response) {
                            if (response.status) {
                                $('.field2').html(response.output);
                                $('.next-nav').show()
                                $('.back-nav').show()
                                $('.current').removeClass('current').hide().next().show().addClass('current');
                                $('#progressbar li.active').next().addClass('active');
                                $(window).scrollTop(0);
                            } else {
                                $('<div class="message-notice notice message" id="noticemsg">'+response.message+'</div>').insertAfter('#progressbar');
                            }
                        }
                    });
                }
               // alert(place.address_components[0].long_name);
            });
        }
        let initializedMapAutoComplete = true
        $(document).on('focus input', '#area_landmark', function() {
            if (initializedMapAutoComplete) {
                initializeMapAutoComplete()
                initializedMapAutoComplete = false
            }
        })


        $(document).on('click', '#vet-consulting .current-location', function() {
            var location = {
                latitude:'',
                longitude:''
            };
            $("#noticemsg").remove();
            if (navigator.geolocation){
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                console.log('navigator')
            }
            function showPosition(position){ 
                location.latitude = position.coords.latitude;
                location.longitude = position.coords.longitude;
                var geocoder = new google.maps.Geocoder();
                var latLng = new google.maps.LatLng(location.latitude, location.longitude);

             if (geocoder) {
                geocoder.geocode({ 'latLng': latLng}, function (results, status) {
                   if (status == google.maps.GeocoderStatus.OK) {

                        var arrAddress = results[0].address_components;
                        var city ='';

                        $.each(arrAddress, function (i, address_component) {
                            for (var j = 0; j < address_component.types.length; j++) {
                                if (address_component.types[j] == "locality"){
                                    city = address_component.long_name;
                                    return false;
                                }
                            }
                        });
                        let postData = {
                            city: city,
                            place: results[0].formatted_address
                        };
                        $('#vet-consulting input#area_landmark').val(results[0].formatted_address)
                        $.ajax({
                            url: urlBuilder.build('services/vet/detectclinic'),
                            method:"POST",
                            data: postData,
                            showLoader: true,
                            success:function(response) {
                                if (response.status) {
                                    $('.field2').html(response.output);
                                    $('.next-nav').show()
                                    $('.back-nav').show()
                                    $('.current').removeClass('current').hide().next().show().addClass('current');
                                    $('#progressbar li.active').next().addClass('active');
                                    $(window).scrollTop(0);
                                } else {
                                    $('<div class="message-notice notice message" id="noticemsg">'+response.message+'</div>').insertAfter('#progressbar');
                                }
                            }
                        });
                    }
                    else {
                        $("#search_location .page.messages .messages").html('<div class="message-notice notice message" data-ui-id="message-notice"><div>Dear customer, we are unable to detect your location. Please try searching.</div></div>');
                        $("#search_location .page.messages").show();
                    }
                });
              }
            }
        });
    }
});