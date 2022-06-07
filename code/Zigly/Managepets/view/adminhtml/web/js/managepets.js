/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
define([
    'jquery', 'Magento_Ui/js/modal/modal','Magento_Ui/js/modal/confirm','jquery/ui',"mage/calendar"
], function ($, modal, confirmation) {
    'use strict';

    return function (config) {
        var uploadurl = config.AjaxUrl;
        var dateToday = new Date();
        var dateToday = new Date();
        $('body').on('focus',"#pet_dob", function(){
          $(this).calendar({
          showsTime: false,
          dateFormat: 'yy-mm-dd',
          buttonImage: config.calenderimage,
          yearRange: "-120y:c+nn",
          buttonText: "Select Date", maxDate: "-1d", changeMonth: true, changeYear: true, showOn: "both",
          onSelect: function (selectedDate, inst) {
            var currentdate = new Date();
            var seldate =  new Date(selectedDate);
            var diff =(currentdate.getTime() - seldate.getTime()) / 1000;
            var diffYear = diff/(60 * 60 * 24);
            diffYear = Math.abs(Math.round(diffYear/365.25));
            $('#age_year').val(diffYear);
            var months;
            months = (currentdate.getFullYear() - seldate.getFullYear()) * 12;
            months -= seldate.getMonth();
            months += currentdate.getMonth();
            months <= 0 ? 0 : months;
            if(months >=12){
                months =months -(12*diffYear);
            }
            $('#age_month').val(months);
         }
        });
        });
        var options = {
            type: 'slide',
            responsive: true,
            innerScroll: false,
            title: $.mage.__('Add Pet'),
            buttons: [{
                text: $.mage.__('Save'),
                class: 'primary',
                click: function () {
                    if(!$('#type').val() || !$('#gender').val() || !$('#breed').val() || !$('#pet_dob').val() || !$('#age_year').val() || !$('#age_month').val() || !$('#name').val()){
                        $('#managepet-form').trigger('submit');
                    }else{
                          $.ajax({
                              url: config.saveajaxUrl,
                              type: 'POST',
                              dataType: 'json',
                              showLoader: true,
                              data: $('#managepet-form').serialize(),
                              complete: function(response) {
                                  let jsondata = response.responseJSON;
                                  if (jsondata.success == 1) {
                                    $("#managepets_view_customer_grid").remove();
                                    $(jsondata.output).insertAfter( "#appendgriddata" );
                                    var modalContainer = $("#modal-overlay");
                                    modalContainer.modal('closeModal');
                                  }
                              },
                              error: function (xhr, status, errorThrown) {
                                  console.log('Error happens. Try again.');
                              }
                          });
                    }
                }
            },{
                text: $.mage.__('Close'),
                class: '',
                click: function () {
                  this.closeModal();
                         // $('#modalcustom-form').trigger('submit');
                }
            }]
        };
        var modal_overlay_element = $('#modal-overlay');
        var popup = modal(options, modal_overlay_element);
        $(document).on('click','#add-new-pet-button',function(){
            var  data = new FormData();
            data.append('form_key',window.FORM_KEY);
            data.append('petid',0);
            data.append('customerid',config.customerid);
            $.ajax({
                url: config.loadformurl,
                type: 'POST',
                contentType: false,
                processData: false,
                showLoader: false,
                data: data,
                 complete: function(response) {
                      let jsondata = response.responseJSON;
                      if (jsondata.success == 1) {
                        $("#modal-overlay").html(jsondata.output);
                        var modalContainer = $("#modal-overlay");
                        modalContainer.modal('openModal');
                        $('.modals-overlay').remove();
                        $('#modal-overlay').trigger('contentUpdated');
                      }
                  },
                  error: function (xhr, status, errorThrown) {
                      console.log('Error happens. Try again.');
                  }
            });
        });
        $(document).on('change', '#type', function() {
            var speciesid = $(this).val();
            var breedspeci = $('#breed').find(":selected").attr('species_id');
            if(breedspeci !=speciesid){
              $('#breed').val('');
            }
            $("#breed > option").each(function() {
              if(speciesid == $(this).attr('species_id')){
                $(this).show();
              }else{
                if($(this).attr('value')){
                    $(this).hide();
                }

              }
            });
        });
        $("#filediv").click(function(e) {
            $("#imageUpload").click();
        });
        $(document).on('click', '.action-remove', function(){
            var removecontent = $(this).parent().parent();
            removecontent.parent().parent().remove();
            $('#imageappend').show();
        });
        $(document).on('change', '#fileupload', function(e) {
            var files = e.target.files,
            filesLength = files.length,
            self = this;
            var  data = new FormData();
            data.append('form_key',window.FORM_KEY);
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
                      let data = response.responseJSON
                      if( data.type == 'image'){
                          var htmlcontent = "<div class=\"image item\" data-role=\"image\" id=\"imagecustomdiv\"><div class=\"product-image-wrapper\"><input type=\"hidden\" accept=\"image/jpeg, image/png, image/jpg,\" name=\"filedatas[]\" value=\"" + data.file + "\"><img class=\"product-image\" data-role=\"image-element\" src=\"" + data.url + "\" alt=\"\"><div class=\"actions\"><div class=\"tooltip\"><button type=\"button\" class=\"action-remove\" data-role=\"delete-button\" title=\"Delete image\"><span>Delete image</span></button></div></div></div></div>";
                          $('#imagecustomdiv').remove();
                          $('#imageappend').hide();
                          $(htmlcontent).insertBefore("#imageappend");
                      }else{
                          alert("This type wont allowed");
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
        $(document).on('click','.edit_pet',function(){
            var  data = new FormData();
            var petid = $(this).attr('pet_id');
            data.append('form_key',window.FORM_KEY);
            data.append('petid',petid);
            data.append('customerid',config.customerid);
            $.ajax({
                url: config.loadformurl,
                type: 'POST',
                contentType: false,
                processData: false,
                showLoader: false,
                data: data,
                 complete: function(response) {
                      let jsondata = response.responseJSON;
                      if (jsondata.success == 1) {
                        $("#modal-overlay").html(jsondata.output);
                        var modalContainer = $("#modal-overlay");
                        modalContainer.modal('openModal');
                        $('.modals-overlay').remove();
                        $('#modal-overlay').trigger('contentUpdated');
                      }
                  },
                  error: function (xhr, status, errorThrown) {
                      console.log('Error happens. Try again.');
                  }
            });
        });
        $(document).on('click', '.delete_pet', function(){
          var petid = $(this).attr('pet_id');
          var  data = new FormData();
          data.append('form_key',window.FORM_KEY);
          data.append('petid',petid);
          data.append('customerid',config.customerid);
          confirmation({
              title: 'Delete the pet',
              content: 'Are you sure you want to delete this pet ?',
              actions: {
                  confirm: function () {
                      $.ajax({
                          url: config.deleteajaxUrl,
                          type: 'POST',
                          contentType: false,
                          processData: false,
                          showLoader: true,
                          data: data,
                           complete: function(response) {
                                let jsondata = response.responseJSON;
                                if (jsondata.success == 1) {
                                  $("#managepets_view_customer_grid").remove();
                                  $(jsondata.output).insertAfter( "#appendgriddata" );
                                  var modalContainer = $("#modal-overlay");
                                  modalContainer.modal('closeModal');
                                }
                            },
                            error: function (xhr, status, errorThrown) {
                                console.log('Error happens. Try again.');
                            }
                      });
                  },

                  cancel: function () {
                      return false;

                  },
                  always: function () {}
              }
          });
        });
    }
});
