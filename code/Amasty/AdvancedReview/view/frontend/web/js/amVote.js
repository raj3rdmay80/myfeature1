 define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Amasty_AdvancedReview/vendor/fancybox/jquery.fancybox.min',
     'mage/translate',
    'mage/cookies',
], function ($, modal, fancybox) {
     'use strict';

    $.widget('mage.amVote', {
        options: {},

        helpfulCounter: null,

        _create: function (options) {
            this.url = this.options['url'];
            this.reviewId = this.element.attr('id').replace(/[^\d]/gi, '');
            this.plus = this.element.find('.amreview-plus');
            this.minus = this.element.find('.amreview-minus');
            this.helpfulCounter = this.element.closest('[data-amreview-js="review-entity"]').find('.amrev-voteqty');

            var self = this;
            this.plus.on('click', function (item) {
                self.clickPlus();
            });

            this.minus.on('click', function (item) {
                self.clickMinus();
            });

            var formKey = $.mage.cookies.get('form_key');
            if (formKey) {
                this.element.find('input[name="form_key"]').val(formKey);
            }

            this.initializeBlock();
        },

        initializeBlock: function () {
            var self = this,
                key = this.element.find('[name="form_key"]').val(),
                data = 'type=update&form_key=' + key + '&review=' + this.reviewId,
                url = self.url + (self.url.indexOf('?') === -1 ? '?' : '&') + Math.random();

            $.ajax({
                url: url,
                data: data,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response && response.success) {
                        self.updateVote(response);
                    }
                }
            });
        },

        clickPlus: function () {
            if (!this.element.hasClass('disabled')) {
                this.element.addClass('disabled');
                this.sendAjax('plus');
            }
        },

        clickMinus: function () {
            if (!this.element.hasClass('disabled')) {
                this.element.addClass('disabled');
                this.sendAjax('minus');
            }
        },

        updateVote: function (response) {
            this.plus.find('.amreview-count').text(response.data.plus);
            this.minus.find('.amreview-count').text(response.data.minus);
            this.helpfulCounter.text($.mage.__('%1 people found this helpful').replace('%1', +response.data.plus));

            if (response.voted.plus > 0) {
                this.plus.addClass('-voted');
            } else {
                this.plus.removeClass('-voted');
            }

            if (response.voted.minus > 0) {
                this.minus.addClass('-voted');
            } else {
                this.minus.removeClass('-voted');
            }
        },

        sendAjax: function ($type) {
            var self = this,
                key = this.element.find('[name="form_key"]').val(),
                data = 'type=' + $type + '&form_key=' + key + '&review=' + this.reviewId;

            $.ajax({
                url: self.url,
                data: data,
                type: 'post',
                dataType: 'json',
                success: function (response) {
                    var result = $('<div/>', {
                            class: 'message'
                        }),
                        html = $('<div/>');
                    if (response && response.success) {
                        html.html(response.success).appendTo(result);
                        result.addClass('success');
                        self.updateVote(response);
                    }
                    if (response && response.error) {
                        html.html(response.error).appendTo(result);
                        result.addClass('error');
                    }

                    self.element.removeClass('disabled');
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $.fancybox(errorThrown);
                }
            });
        }
    });

    return $.mage.amVote;
});
