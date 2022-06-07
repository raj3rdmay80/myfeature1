/**
 * Copyright (C) 2020  Zigly


 * @package  Zigly_CityScreen
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'jquery/ui',
    'https://maps.googleapis.com/maps/api/js?key=AIzaSyBaWv8RYNF_wfDNIKrz3MzlfD_WKYfUDnI&libraries=places,drawing&language=en',
    'jquery/jquery.cookie'
], function ($, modal) {
    'use strict';

    return function (config) {
        var options = {
            type: 'popup',
            responsive: true,
            title: 'Where do you live, Hooman?',
            modalClass: 'city-screen-modal',
            innerScroll: true,
            buttons: [{
                text: $.mage.__(),
                class: 'mymodalcity',
                click: function () {
                    this.closeModal();
                }
            }],
            opened: function() {
                $("#autosearch").val('');
            }
        };
        let shownSecondModal = false;
        var popup = modal(options, $('#popup-modal'));
        $(document).on('click', ".select_city" ,function(){
            $("#location-modal").show();
            $(".location-overlay").show();
            $('body').addClass('_has-modal')
            $("#errorcity").remove();
        });

        $(document).on('click', '.enter_location' ,function(){
            shownSecondModal = true;
            $("#popup-modal").modal("openModal");
            $("#location-modal").hide();
            $(".location-overlay").hide();
            $('body').removeClass('_has-modal')
            $("#errorcity").remove();
        });
        $(document).on('click', "#location-modal button.action-close" ,function(){
            $("#location-modal").hide();
            $(".location-overlay").hide();
            $('body').removeClass('_has-modal')
        });

        if ($('body.services-grooming-index').length || $('body.services-grooming-center').length) {
            //Mandatory
            $(document).ready(function(){
                var acookie = $.cookie("city_screen");
                if (!acookie){
                    var showDetectLocation = setInterval(function() {
                        if ($("#location-modal").length) {
                            clearInterval(showDetectLocation);
                            $("#location-modal").show();
                            $(".location-overlay").show();
                            $('body').addClass('_has-modal')
                            $("#errorcity").remove();
                        }
                    }, 200);
                }
            });
            $('#popup-modal').on('modalclosed', function(e) { 
                $("#errorcity").remove();
                if ($("#selectedValue").text() == "Location"){
                    $(this).modal("openModal");
                    e.preventDefault();
                    $('<div class="mage-error" id="errorcity">Please select your city</div>').insertBefore('.select_label');
                }
            });
            $(document).on('click', ".location-overlay" ,function(e){
                $("#errorcity").remove();
                if ($("#selectedValue").text() == "Location" && !shownSecondModal){
                    $('<div class="mage-error" id="errorcity">Please select from the below.</div>').insertBefore('.action-bar');
                    e.preventDefault();
                } else {
                    $("#location-modal").hide();
                    $(".location-overlay").hide();
                    $('body').removeClass('_has-modal')
                }
            });
        } else {
            $(document).on('click', ".location-overlay" ,function(){
                $("#errorcity").remove();
                /*if ($("#selectedValue").text() == "Location" && !shownSecondModal){
                    $('<div class="mage-error" id="errorcity">Please select from the below.</div>').insertBefore('.action-bar');
                    e.preventDefault();
                } else {*/
                    $("#location-modal").hide();
                    $(".location-overlay").hide();
                    $('body').removeClass('_has-modal')
                // }
            });

        }

        $(document).on('click', '.detect_location' ,function(){
            $("#errorcity").remove();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                (position) => {
                    var geocoder = new google.maps.Geocoder();
                    var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    console.log('----Detected latlng ------')
                    console.log(position.coords.latitude, position.coords.longitude)
                    geocoder.geocode({'latLng': latlng}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var arrAddress = results[0].address_components;
                            console.log('----Retrieved address_components ------')
                            console.log(arrAddress)
                            var city = '';
                            var postCode = '';
                            var state = '';
                            var postCode = '';
                            var address1 = '';
                            var address2 = '';
                            $.each(arrAddress, function (i, address_component) {
                                for (var j = 0; j < address_component.types.length; j++) {
                                    if (address_component.types[j] == "street_number") {
                                        address1 = address_component.long_name;
                                    } else if (address_component.types[j] == "route") {
                                        address1 = address1 + address_component.long_name;
                                    } else if (address_component.types[j] == "sublocality") {
                                        address2 = address_component.long_name;
                                    } else if (address_component.types[j] == "locality") {
                                        city = address_component.long_name;
                                    } else if (address_component.types[j] == "administrative_area_level_1") {
                                        state = address_component.long_name;
                                    } else if (address_component.types[j] == "postal_code") {
                                        postCode = address_component.short_name;
                                    }
                                }
                            });

                            let postLocation = {
                                city: city,
                                postcode: postCode,
                                address1: address1,
                                address2: address2,
                                state: state,
                                latlng: {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude
                                }
                            };

                            $.ajax({
                                    url: config.geoCheckUrl,
                                    method:"POST",
                                    data: postLocation,
                                    showLoader: true,
                                    success:function(response) {
                                        if (response.success) {
                                            var selectedCity = $.cookie("city_screen");
                                            $('#selectedValue').text(selectedCity);
                                            $("#location-modal").hide();
                                            $(".location-overlay").hide();
                                            $('body').removeClass('_has-modal')
                                            if ($("body").hasClass("services-grooming-center")) {
                                                window.location.reload();
                                            }
                                            if ($("body").hasClass("services-grooming-index")) {
                                                window.location.reload();
                                            }
                                            if ($("body").hasClass("checkout-index-index")) {
                                                window.location.reload();
                                            }
                                        } else {
                                            $('<div class="mage-error" id="errorcity"> Dear customer, we are unable to detect your location. Please select your city.</div>').insertBefore('.action-bar');
                                        }
                                    }
                                });
                        } else {
                          $('<div class="mage-error" id="errorcity"> Dear customer, we are unable to detect your location. Please select your city.</div>').insertBefore('.action-bar');
                        }
                    });
                },
                () => {
                    $('<div class="mage-error" id="errorcity">Please allow us to detect your location.</div>').insertBefore('.action-bar');
                });
            } else {
                $('<div class="mage-error" id="errorcity">Browser doesn\'t support detecting location.</div>').insertBefore('.action-bar');
            }
        });
        $(document).ready(function(){
            $( "#autosearch" ).autocomplete({
                source: config.cities,
                minLength: 2,
                select: function( event, ui ) {
                    $(this).val(ui.item.value);
                    $(this).blur();
                    $(this).focus();
                },
                focus: function(event, ui){
                    event.preventDefault();
                },
                open: function() {
                    $('#loader').addClass('loading');
                    $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
                },
                close: function() {
                    $('#loader').removeClass('loading');
                    $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
                }
            });
            $('#autosearch').on('click',function(){
                $("#errorcity").remove();
            });
            var selectedCity = $.cookie("city_screen");
            if(selectedCity != null && selectedCity != ''){
                $("#selectedValue").text(selectedCity);
            }
            $(document).on('click', '.ui-corner-all',function(){
                $("#errorcity").remove();
                var searched = $("#autosearch").val();
                var cities = config.cities;
                if(jQuery.inArray(searched, cities)!='-1' && searched){
                    var customurl = config.AjaxUrl;
                    $.ajax({
                        url: customurl,
                        type: 'POST',
                        dataType: 'json',
                        showLoader: true,
                        data: {
                            searched : searched
                        },
                        complete: function(response) {
                            $("#popup-modal").modal("closeModal");
                            var selectedCity = $.cookie("city_screen");
                            $('#selectedValue').text(selectedCity);
                            if ($("body").hasClass("services-grooming-center")) {
                                window.location.reload();
                            }
                            if ($("body").hasClass("checkout-index-index")) {
                                window.location.reload();
                            }
                            if ($("body").hasClass("services-grooming-index")) {
                                window.location.reload();
                            }
                        },
                        error: function (xhr, status, errorThrown) {
                            console.log(xhr.status);
                        }
                    });
                }
            });
        });
    }
});