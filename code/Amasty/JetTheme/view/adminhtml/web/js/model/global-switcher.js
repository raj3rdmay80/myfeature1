/**
 * Global config model
 * @api
 */

define([
    'ko',
    'jquery',
    'underscore',
    'Amasty_JetTheme/js/model/colors-config',
    'Amasty_JetTheme/js/model/custom-footer'
], function (ko, $, _, colorsModel, footerModel) {
    'use strict';

    var globalConfig = ko.observable({}),
        errorMessage = ko.observable(''),
        defaultGlobalConfig = ko.observable({}),
        isRestoreToDefault = ko.observable(false);

    /**
     * @param {Object} config
     * @returns {void}
     */
    function handleDefaultConfig(config) {
        if (!_.isEmpty(defaultGlobalConfig())) {
            isRestoreToDefault(!isRestoreToDefault());
            defaultGlobalConfig({});
        }

        _.each(config, function (data, key) {
            defaultGlobalConfig()[key] = $('#' + key).val();
        });

        if (_.has(config, 'color_scheme')) {
            colorsModel.setConfig(config['color_scheme']);
            colorsModel.isResetColor(false);
        } else {
            colorsModel.isResetColor(true);
        }

        if (_.has(config, 'custom_footer_layout_config')) {
            footerModel.handleInitialConfig(config['custom_footer_layout_config']);
        }

        globalConfig(config);
    }

    /**
     * Get global config
     * @returns {Object}
     */
    function getConfig() {
        return globalConfig();
    }

    /**
     * Set config
     * @param {Object} config
     * @returns {void}
     */
    function setConfig(config) {
        handleDefaultConfig(config);
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
        globalConfig: globalConfig,
        defaultGlobalConfig: defaultGlobalConfig,
        errorMessage: errorMessage,
        isRestoreToDefault: isRestoreToDefault,
        getConfig: getConfig,
        setConfig: setConfig,
        setErrorMessage: setErrorMessage
    };
});
