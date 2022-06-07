/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    "mage/calendar",
    "mage/mage"
], function ($) {
    'use strict';

    return function (config) {
        var uploadurl = config.AjaxUrl;
        var dateToday = new Date();
        $("#pet_dob").calendar({
          showsTime: false,
          dateFormat: 'yy-mm-dd',
          buttonImage: config.calenderimage,
          yearRange: "-20y:c+nn",
          maxDate: new Date(),
          buttonText: "Select Date", maxDate: "-1d", changeMonth: true, changeYear: true, showOn: "both",
          onSelect: function (selectedDate, inst) {
            $('#age_year').prop('readonly', true);
            $('#age_month').prop('readonly', true);
            var currentdate = new Date();
            var seldate =  new Date(selectedDate);
            var year = (function() {
                if(seldate.getMonth() == currentdate.getMonth()) {
                    if(seldate.getDate() > currentdate.getDate()) {
                        return (currentdate.getFullYear() - 1) - seldate.getFullYear();
                    }
                    else {
                        return currentdate.getFullYear() - seldate.getFullYear();
                    }
                }
                else {
                    if(seldate.getMonth() > currentdate.getMonth()) {
                        return (currentdate.getFullYear() - 1) - seldate.getFullYear();
                    }
                    else {
                        return currentdate.getFullYear() - seldate.getFullYear();
                    }
                }
            }());
            /*var diff =(currentdate.getTime() - seldate.getTime()) / 1000;
            var diffYear = diff/(60 * 60 * 24);
            diffYear = Math.abs(Math.round(diffYear/365.25));*/
            $('#age_year').val(year);
            var month = (function() {
                if(currentdate.getMonth() >= seldate.getMonth()) {
                    if(currentdate.getDate() >= seldate.getDate()) {
                        return currentdate.getMonth() - seldate.getMonth();
                    }
                    else {
                        if((currentdate.getMonth() - 1) >= seldate.getMonth()) {
                            return (currentdate.getMonth() - 1) - seldate.getMonth();
                        }
                        else {
                            return ((currentdate.getMonth() - 1) + 12) - seldate.getMonth();
                        }
                    }
                }
                else {
                    if(currentdate.getDate() >= seldate.getDate()) {
                        return (currentdate.getMonth() + 12) - seldate.getMonth();
                    }
                    else {
                        return ((currentdate.getMonth() - 1) + 12) - seldate.getMonth();
                    }
                }
            }());
            /*var months;
            months = (currentdate.getFullYear() - seldate.getFullYear()) * 12;
            months -= seldate.getMonth();
            months += currentdate.getMonth();
            months <= 0 ? 0 : months;
            if(months >=12){
                months =months -(12*diffYear);
            }
            months = Math.abs(months);*/
            $('#age_month').val(month);
         }
        });
        $("#filediv").click(function(e) {
            $("input[name='avatar_file']").val('');
            $("input[name='avatar_file']").prop('checked', false);
            $("#imageUpload").click();
        });
        $(document).on('click', '.remove', function(){
            $(this).parent(".pip").remove();
        });
       //dropdown breed
        // $(document).on('change', '#type', function() {
        //     var speciesid = $(this).val();
        //     var breedspeci = $('#breed').find(":selected").attr('species_id');
        //     if(breedspeci !=speciesid){
        //       $('#breed').val('');
        //     }
        //     $("#breed > option").each(function() {
        //       if(speciesid == $(this).attr('species_id')){
        //         $(this).show();
        //       }else{
        //         if($(this).attr('value')){
        //             $(this).hide();
        //         }

        //       }
        //     });
        // });
        $(document).on('change', '#imageUpload', function(e) {
            $("#errorpet").remove();
            var files = e.target.files,
            filesLength = files.length,
            self = this;
            if(filesLength == 0) return true;
            var fileSize = files[0].size;
            var size = 512000;
            if (!(fileSize <= size)) {
                $('<div class="mage-error" id="errorpet">The file size should not exceed 5MB.</div>').insertAfter('.filediv');
                return false;
            }
            var  data = new FormData();
            data.append('image',files[0]);
            var uploadurl = config.AjaxUrl;
            $.ajax({
              url: uploadurl,
              type: 'POST',
              contentType: false,
              processData: false,
              showLoader: true,
              data: data,
              complete: function(response) {
                  console.log(response);
                  console.log(response.responseJSON);
                  if(response.responseJSON.error == 0){
                      $(self).val('');
                      $(".filediv").hide();
                      let data = response.responseJSON
                      if( data.type == 'image'){
                          $(".pip").remove();
                          $(".image-pip").remove();
                          $("<div class=\"image-pip\"><span class=\"pip\">" +
                          "<input type=\"hidden\" accept=\"image/jpeg, image/png, image/jpg,\" name=\"" + $(self).attr('name') + "\" value=\"" + data.file + "\">"+
                          "<img class=\"imageThumb\" src=\"" + data.url + "\" title=\"" + data.name + "\"/>" +
                          "<span class=\"remove\">x</span></span></div>").insertAfter(self);
                      }else{
                          $("<div class=\"image-pip\"><span class=\"pip\">" +
                          "<input type=\"hidden\" accept=\"image/jpeg, image/png, image/jpg,\" name=\"" + $(self).attr('name') + "\" value=\"" + data.file + "\">"+data.name+
                          "<span class=\"remove\">x</span></span></div>").insertAfter(self);
                      }
                  }else{
                      alert(response.responseJSON.error);
                  }
              },
              error: function (xhr, status, errorThrown) {
                  console.log('Error happens. Try again.');
              }
            });
        });
        $(document).ready(function(){
            $('#breedname').prop('readonly', true);
            $(".avatar-cat").css("display","none")
            $(".avatar-dog").css("display","block")
            var type = $("input[name='type']:checked").val();
            if ($('input[name=type]:checked').size() == 0) {
                $("input[name='type'][value='1']").prop('checked', true);
                $('#breedname').prop('readonly', false);
                $("input[name='avatar_file'][value='images/ava_dog-1.png']").prop('checked', true);
            }
            if (type == 1) {
                $('#breedname').prop('readonly', false);
                $(".avatar-cat").css("display","none")
                $(".avatar-dog").css("display","block")
            } else if (type == 2) {
                $('#breedname').prop('readonly', false);
                $(".avatar-cat").css("display","block")
                $(".avatar-dog").css("display","none")
            }
            $(document).on('click', '.remove',function(){
                $(".filediv").show();
                $('.custom-image').attr('src','');
                $('#filedatas').val('');
                $(".image-pip").hide();
                $(".upload-custom-image").hide();
            });
            if ($('.custom-image').attr('src')) {
                $(".filediv").hide();
                $("input[name='avatar_file']").val('');
            }
            $(".managepet-form").find('input[name="type"]').keypress(
                function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        event.preventDefault();
                        $(".save-new-pet").trigger('click')
                    }
                }
            );
            $(".managepet-form").find('input[name="gender"]').keypress(
                function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        event.preventDefault();
                        $(".save-new-pet").trigger('click')
                    }
                }
            );
            $(".managepet-form").find('input[name="breedname"]').keypress(
                function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        event.preventDefault();
                        $(".save-new-pet").trigger('click')
                    }
                }
            );
            $(".managepet-form").find('input[name="pet_dob"]').keypress(
                function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        event.preventDefault();
                        $(".save-new-pet").trigger('click')
                    }
                }
            );
            $(".managepet-form").find('input[name="age_year"]').keypress(
                function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        event.preventDefault();
                        $(".save-new-pet").trigger('click')
                    }
                }
            );
            $(".managepet-form").find('input[name="age_month"]').keypress(
                function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        event.preventDefault();
                        $(".save-new-pet").trigger('click')
                    }
                }
            );
            $(".managepet-form").find('input[name="name"]').keypress(
                function (event) {
                    var code = event.keyCode || event.which;
                    if (code === 13) {
                        event.preventDefault();
                        $(".save-new-pet").trigger('click')
                    }
                }
            );
            var breedValue = config.breedValue;
            var tmpArr = breedValue.slice(0);
            tmpArr.unshift({});
            var breedArr = jQuery.extend.apply(this, tmpArr);
            $( "#breedname" ).autocomplete({
                source: function( request, response ) {
                    var type = $("input[name='type']:checked").val();
                    var search = $('input[name="breedname"]').val();
                    $.ajax({
                        url: config.getBreed,
                        dataType: 'json',
                        type: "POST",
                        data: {
                            type: type,
                            searched : search
                        },
                        success: function(data) {
                            response($.map(data, function (el) {
                                return {
                                    label: el.label,
                                    value: el.label
                                };
                            }));
                        }
                    });
                },
                minLength: 2,
                select: function( event, ui ) {
                    $(this).val(ui.item.value);
                    $(this).blur();
                    $(this).focus();
                    $('#breed_id').val(breedArr[ui.item.value]);
                },
                change: function() {
                    $('#breed_id').val('');
                },
                focus: function(event, ui){
                    /*event.preventDefault();*/
                },
                open: function() {
                    $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
                },
                close: function() {
                    $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
                }
            });
            $(document).on('change', "input[name='type']",function(){
                $('#breed_id').val('');
                $('#breedname').val('');
                $('#breedname').prop('readonly', false);
                var type = $("input[name='type']:checked").val();
                var selectedBreed = config.selectedBreed;
                if(type == config.selectedType) {
                    $('#breedname').val(selectedBreed);
                    $("input[name='breed']").val(selectedBreed)
                }
                if (type == 1) {
                    $(".avatar-cat").css("display","none")
                    $(".avatar-dog").css("display","block")
                    if (!$('.custom-image').attr('src')) {
                        $("input[name='avatar_file'][value='images/ava_dog-1.png']").prop('checked', true);
                    }
                } else if (type == 2) {
                    $(".avatar-cat").css("display","block")
                    if (!$('.custom-image').attr('src')) {
                        $("input[name='avatar_file'][value='images/ava_cat.png']").prop('checked', true);
                    }
                    $(".avatar-dog").css("display","none")
                }
            });
            $('body').click(function() {
               if ($("input[name='pet_dob']").val()) {
                    $('#age_year').prop('readonly', true);
                    $('#age_month').prop('readonly', true);
                } else {
                    $('#age_year').prop('readonly', false);
                    $('#age_month').prop('readonly', false);
                }
            });
            $(document).on('click', '#pet_dob, .ui-datepicker-trigger',function(){
                if ($("input[name='pet_dob']").val()) {
                    $('#age_year').prop('readonly', true);
                    $('#age_month').prop('readonly', true);
                } else {
                    $('#age_year').prop('readonly', false);
                    $('#age_month').prop('readonly', false);
                }
            });
            $(document).on('click', '.save-new-pet',function(){
                $("#errorpet").remove();
                if ($("input[name='type']:checked").val() == null) {
                    $('<div class="mage-error" id="errorpet">Please select your furry friend</div>').insertAfter('.fields.type');
                    return false;
                }
                if ($("input[name='gender']:checked").val() == null) {
                    $('<div class="mage-error" id="errorpet">Please select gender</div>').insertAfter('.fields.gender');
                    return false;
                }
                if (!$("input[name='breed']").val()) {
                    $('<div class="mage-error" id="errorpet">Please select breed</div>').insertAfter('.fields.breed');
                    return false;
                }
                if (!$("input[name='age_year']").val() || !$("input[name='age_month']").val()) {
                    $('<div class="mage-error" id="errorpet">This is a required field.</div>').insertAfter('.fields.age-year');
                    return false;
                }
                if ($("input[name='age_year']").val() > 21) {
                    $('<div class="mage-error" id="errorpet">Please enter valid year.</div>').insertAfter('.fields.age-year');
                    return false;
                }
                if ($("input[name='age_year']").val() == 0 && $("input[name='age_month']").val() == 0) {
                    $('<div class="mage-error" id="errorpet">Please enter valid age.</div>').insertAfter('.fields.age-year');
                    return false;
                }
                if ($("input[name='age_month']").val() > 12 || $("input[name='age_month']").val() < 0) {
                    $('<div class="mage-error" id="errorpet">Please enter valid month.</div>').insertAfter('.fields.age-year');
                    return false;
                }
                if ($("input[name='age_year']").val().length > 2 || !$("input[name='age_month']").val().length > 2) {
                    $('<div class="mage-error" id="errorpet">Please enter a value less than or equal to 2.</div>').insertAfter('.fields.age-year');
                    return false;
                }
                if ($("input[name='age_year']").val() < 0 || !$("input[name='age_month']").val() < 0 || !($.isNumeric($("input[name='age_year']").val())) || !($.isNumeric($("input[name='age_month']").val()))) {
                    $('<div class="mage-error" id="errorpet">Please enter a value greater than or equal to 0.</div>').insertAfter('.fields.age-year');
                    return false;
                }
                $('input[name="name"]').validation();
                if(!$('input[name="name"]').validation('isValid')){
                    return false;
                }
                if (/[^a-zA-Z0-9\s]/.test($("input[name='name']").val())) {
                    $('<div class="mage-error" id="errorpet">Please enter valid name</div>').insertAfter('.fields.pet-name');
                    return false;
                }
            });
        });
    }
});
