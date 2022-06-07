/**
 * Custom footer model
 * @api
 */

define([
    'ko',
    'underscore',
    'jquery'
], function (ko, _, $) {
    'use strict';

    var footerBlocks = ko.observable(null),
        footerLayouts = ko.observable(null),
        fieldValue = ko.observable({}),
        activeLayout = ko.observable(null);

    /**
     * Map active blocks with initial config
     * @param {Object} blockData
     * @param {Number} elementValue
     * @returns {string}
     */
    // eslint-disable-next-line consistent-return
    function mapBlocksConfig(blockData, elementValue) {
        var fieldData = fieldValue();

        if (fieldData) {
            _.each(fieldData, function (configItem) {
                var isBlockExist = false;

                if (configItem && typeof configItem === 'object') {
                    _.each(configItem, function (configArray) {
                        var isContains = _.contains(configArray, blockData.name);

                        if (isContains) {
                            isBlockExist = true;

                            if (!elementValue) {
                                configArray.splice(_.indexOf(configArray, blockData.name), 1);
                            }
                        }
                    });

                    if (!isBlockExist && elementValue) {
                        configItem[0].push(blockData.name);
                    }

                    blockData.active(elementValue);
                }
            });

            return fieldData;
        }
    }

    /**
     * @param {Object} config
     * @returns {void}
     */
    function setFieldValue(config) {
        fieldValue(_.extend(fieldValue(), config));
        fieldValue.valueHasMutated();
    }

    /**
     * Set block listeners
     * @param {Object} blockData
     * @returns {Function}
     */
    function setBlockListeners(blockData) {
        var $element = $('#' + blockData['config-id']),
            value = +$element.val();

        if ($element.length) {
            $element.on('change', function (event) {
                var newFieldValue = '';

                value = +event.target.value;

                newFieldValue = mapBlocksConfig(blockData, value);

                setFieldValue(newFieldValue);
            });
        }

        return mapBlocksConfig(blockData, value);
    }

    /**
     * @param {Object} blocks
     * @returns {void}
     */
    function setFooterBlocks(blocks) {
        var fieldData = fieldValue() ? fieldValue() : null;

        _.each(blocks, function (block, key) {
            var configId = _.has(block, 'config_path') ? block['config_path'].replace(/[/]/g, '_') : '';

            block.name = key;
            block['config-id'] = configId;
            block.active = ko.observable(true);

            fieldData = setBlockListeners(block);
        });

        setFieldValue(fieldData);
        footerBlocks(blocks);
    }

    /**
     * @param {Object} layouts
     * @returns {void}
     */
    function setFooterLayouts(layouts) {
        footerLayouts(layouts);
    }

    /**
     * @param {Object} config
     * @returns {void}
     */
    function setFooterConfig(config) {
        setFooterBlocks(config.content);
        setFooterLayouts(config.layouts);
    }

    /**
     * @returns {string}
     */
    function getActiveLayout() {
        return activeLayout();
    }

    /**
     * @param {String} layoutCode
     * @returns {void}
     */
    function setActiveLayout(layoutCode) {
        activeLayout(layoutCode);
        fieldValue(_.extend(fieldValue(), { 'active-layout': getActiveLayout() }));
    }

    /**
     * Set initial active layout & create config for footer block
     * @param {Array} initialConfig
     * @return {void}
     */
    function handleInitialConfig(initialConfig) {
        var config = fieldValue() ? fieldValue() : initialConfig;

        activeLayout(config['active-layout']);
    }

    return {
        footerBlocks: footerBlocks,
        footerLayouts: footerLayouts,
        fieldValue: fieldValue,
        activeLayout: activeLayout,
        setFooterConfig: setFooterConfig,
        handleInitialConfig: handleInitialConfig,
        setActiveLayout: setActiveLayout,
        getActiveLayout: getActiveLayout,
        setFieldValue: setFieldValue
    };
});
