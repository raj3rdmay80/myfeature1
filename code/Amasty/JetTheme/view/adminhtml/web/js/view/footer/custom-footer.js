define([
    'jquery',
    'ko',
    'underscore',
    'uiComponent',
    'uiLayout',
    'mageUtils',
    'Amasty_JetTheme/js/model/custom-footer',
], function ($, ko, _, Component, layout, utils, footerModel) {
    'use strict';

    var layoutNode = {
        // eslint-disable-next-line no-template-curly-in-string
        parent: '${ $.$data.parentName }',
        // eslint-disable-next-line no-template-curly-in-string
        name: '${ $.$data.name }',
        displayArea: 'footers-grid'
    };

    return Component.extend({
        defaults: {
            asyncUiElement: null,
            asyncElementSelector: 'amasty_jettheme_custom_footer_enable_custom_layout',
            isAsyncElementInitialized: false,
            template: 'Amasty_JetTheme/footer/custom-footer',
            layoutConfig: null,
            footerLayouts: null,
            value: null,
            elementValue: {}
        },

        /** @inheritDoc  */
        initialize: function () {
            this._super();

            $.async({
                selector: '#' + this.asyncElementSelector
            }, function (element) {
                this.asyncUiElement = $(element);

                this.asyncElementValue(+this.asyncUiElement.val());

                this.asyncUiElement.on('change', function (event) {
                    this.asyncElementValue(+event.target.value);
                }.bind(this));
            }.bind(this));

            return this;
        },

        /** @inheritDoc */
        initObservable: function () {
            this._super()
                .observe(['value', 'asyncElementValue', 'footerLayouts', 'value']);

            footerModel.fieldValue.subscribe(function (config) {
                this.value(null);
                this.value(JSON.stringify(config));
            }.bind(this));

            this.setFooterData();
            this.initChildren();

            return this;
        },

        /**
         * Set footed data into model
         * @returns {void}
         */
        setFooterData: function () {
            var initialConfig = JSON.parse(this.elementValue);

            footerModel.setFieldValue(initialConfig);
            footerModel.setFooterConfig(JSON.parse(this.layoutConfig));
            footerModel.handleInitialConfig(initialConfig);
        },

        /**
         * Crete & init child components
         * @returns {Object}
         */
        initChildren: function () {
            var layouts = footerModel.footerLayouts(),
                activeLayout = footerModel.getActiveLayout();

            _.each(layouts, function (config) {
                var rendererTemplate,
                    rendererComponent,
                    templateData;

                templateData = {
                    parentName: this.name,
                    name: config.value
                };
                rendererTemplate = _.extend(
                    layoutNode,
                    {
                        component: config.component
                            ? config.component
                            : 'Amasty_JetTheme/js/view/footer/abstract-footer-layout',
                        label: config.label,
                        isVisible: activeLayout === config.value
                    }
                );

                rendererComponent = utils.template(rendererTemplate, templateData);
                layout([ rendererComponent ]);
            }.bind(this));

            return this;
        }
    });
});
