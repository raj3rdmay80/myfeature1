/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
define([
    'jquery', 'Magento_Ui/js/modal/confirm'
], function ($,confirmation) {
    'use strict';

    return function (config) {
        $(document).on('click', '.delete_pet', function(){
          var id = $(this).attr('pet_id');
          var Url  = config.deleteurl+'/id/'+id;
          var name = $(this).closest('p').find('.name').text();
          confirmation({
              title: 'Delete the pet',
              content: 'Are you sure you want to delete this pet '+name+'?',
              actions: {

                  confirm: function () {
                      location.href = Url;
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
