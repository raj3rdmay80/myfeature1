define(['underscore', 'jquery', 'Magento_Ui/js/modal/modal-component', 'mage/url'], function (_, $, Modal, url) 
{
    'use strict';

    return Modal.extend(
    {
        saveData: function () 
        {
            this.applyData();
            var groomerData = this.applied;
            var ajaxUrl = url.build('zigly_groomingservice/grooming/savegrommer');

            var data = {
                'form_key': window.FORM_KEY,
                'data' : groomerData
            };

            $.ajax(
            {
                type: 'POST',
                url: ajaxUrl,
                data: data,
                showLoader: true
            }).done(function (xhr) 
            {
                if (xhr.error) 
                {
                    self.onError(xhr);
                }
            }).fail(this.onError);

            this.closeModal();

            let [firstKey] = Object.keys(groomerData);
            var errorHtmlMessage = '<div id="groomer-error-msg" class="message message-error error"><div data-ui-id="messages-message-error">Assign only one Groomer.</div></div>';
            if(groomerData[firstKey].length > 1) {
                $('.page-main-actions:last').after(errorHtmlMessage);
                setTimeout(function(){
                  $('#groomer-error-msg').remove();
                }, 5000);
            } else {
                this.closeModal();
            }
        },
    });
});
