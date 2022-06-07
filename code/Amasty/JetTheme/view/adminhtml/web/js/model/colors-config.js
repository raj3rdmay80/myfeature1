/**
 * Colors model
 * @api
 */

define([
    'ko'
], function (ko) {
    'use strict';

    var colorsConfig = ko.observable({}),
        errorMessage = ko.observable(''),
        isResetColor = ko.observable(false);

    /**
     * Set colors into observable property
     * @param {Object} colors
     * @returns {void}
     */
    function setConfig(colors) {
        colorsConfig(colors);
    }

    /**
     * Set messages
     * @param {String} message
     * @returns {void}
     */
    function setErrorMessage(message) {
        errorMessage(message);
    }

    return {
        colorsConfig: colorsConfig,
        errorMessage: errorMessage,
        isResetColor: isResetColor,
        setConfig: setConfig,
        setErrorMessage: setErrorMessage
    };
});
