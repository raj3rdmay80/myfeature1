define([
    'jquery',
    'underscore',
    'Amasty_JetTheme/js/view/abstract-switcher-button',
    'Amasty_JetTheme/js/model/global-switcher'
], function ($, _, Button, globalSwitcherModel) {
    'use strict';

    return Button.extend({
        defaults: {
            presetsElementSelector: 'amasty_jettheme_style_switcher_style',
            selectedClass: 'amtheme-selected-row',
            defaultOptionValue: 'food',
            actionConfigKey: 'style_id'
        },
        model: globalSwitcherModel,

        /** @inheritDoc */
        initObservable: function () {
            this._super();

            this.model.globalConfig.subscribe(function (data) {
                this.handleElements(data);
            }.bind(this));

            this.model.isRestoreToDefault.subscribe(function () {
                this.restoreToDefault(this.model.defaultGlobalConfig());
            }.bind(this));

            return this;
        },

        /**
         * @param {Object} config
         * @returns {void}
         */
        restoreToDefault: function (config) {
            _.each(config, function (data, key) {
                var $element = $('#' + key),
                    defaultValue = $element.data('isUseDefault'),
                    useDefaultElement = this.getUseDefaultElement(key);

                this.handleElement('#' + key, data, false);

                if (!useDefaultElement.length) {
                    return;
                }

                if (defaultValue && useDefaultElement.is(':checked') !== defaultValue) {
                    useDefaultElement.trigger('click');
                }
            }.bind(this));
        },

        /**
         * @return {Object} chaining
         */
        afterRenderHandler: function () {
            this._super();

            this.presetsElement = $('#' + this.presetsElementSelector);

            this.setElementListeners();

            return this;
        },

        /**
         * @param {Object} config
         * @returns {void}
         */
        handleElements: function (config) {
            if (config) {
                _.each(config, function (data, key) {
                    var $element = $('#' + key),
                        defaultValue = $element.data('isUseDefault'),
                        useDefaultElement = this.getUseDefaultElement(key);

                    this.handleElement('#' + key, data, true);

                    if (!useDefaultElement.length) {
                        return;
                    }

                    if (!defaultValue) {
                        $element.data('isUseDefault', useDefaultElement.is(':checked'));
                    }

                    if (useDefaultElement.is(':checked')) {
                        useDefaultElement.trigger('click');
                    }
                }.bind(this));
            }
        },

        /**
         * @param {String} elementId
         * @param {Object} data
         * @param {Boolean} isNew
         * @returns {void}
         */
        handleElement: function (elementId, data, isNew) {
            var $element = $(elementId);

            if ($element.length) {
                if (data instanceof Array) {
                    this.handleMultiselect($element, data);
                } else if (data instanceof Object) {
                    $element.val(data.value);
                } else {
                    $element.val(data);
                }

                $element[0].dispatchEvent(new Event('change'));

                this.highlightParents(elementId, isNew);
            }
        },

        getUseDefaultElement: function (elementId) {
            return $('#row_' + elementId).find('#' + elementId + '_inherit');
        },

        /**
         * @param {Element} element
         * @param {Object} data
         * @returns {void}
         */
        handleMultiselect: function (element, data) {
            var options = $(element).find('option');

            _.each(options, function (option) {
                var $option = $(option),
                    optionValue = $option.val(),
                    equalValue = _.find(data, function (value) {
                        return value === optionValue;
                    });

                // eslint-disable-next-line no-unused-expressions
                equalValue ? $option.attr('selected', 'selected') : $option.removeAttr('selected');
            });
        },

        /**
         * @param {String} elementId
         * @param {Boolean} flag
         * @returns {void}
         */
        highlightParents: function (elementId, flag) {
            var $element = $(elementId),
                $parentElement = $element.closest('.section-config'),
                $headerElement = $parentElement.find('.entry-edit-head');

            this._toggleSelectedClass($element.closest('tr'), flag);
            this._toggleSelectedClass($headerElement, flag);

            if (!$headerElement.hasClass('admin__collapsible-block')) {
                return;
            }

            $parentElement.addClass('active');
            $headerElement.find('> a').addClass('open');
            $parentElement.find('.admin__collapsible-block').show();
        },

        /**
         * Add/remove classes on updated elements
         * @private
         * @param {Element} element
         * @param {Boolean} show
         * @returns {void}
         */
        _toggleSelectedClass: function (element, show) {
            var $element = $(element);

            // eslint-disable-next-line no-unused-expressions
            show ? $element.addClass(this.selectedClass) : $element.removeClass(this.selectedClass);
        }
    });
});
