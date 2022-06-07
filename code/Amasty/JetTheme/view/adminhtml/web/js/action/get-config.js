/**
 * Ajax actions
 * @api
 */

define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    return {
        /**
         * Ajax call
         * @param {String} url
         * @param {Object} data
         * @param {Object} model
         * @returns {Deferred}
         */
        getConfig: function (url, data, model) {
            return $.ajax({
                url: url,
                data: this._prepareData(data),
                type: 'POST',
                success: function (response) {
                    if (_.has(response, 'error') && response.error) {
                        model.setErrorMessage(response.message);
                    } else {
                        model.setConfig(response.data);
                    }
                }
            });
        },

        /**
         * Prepare data for ajax
         * @param {Object} data
         * @returns {Object}
         */
        _prepareData: function (data) {
            return $.extend({
                form_key: window.FORM_KEY
            }, data);
        }
    };
});
