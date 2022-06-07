define([
    'uiElement',
    'jquery',
    'underscore'
], function (Element, $, _) {
    'use strict';

    return Element.extend({
        defaults: {
            filesCount: 0,
            part: 1,
            parts: 0,
            filesPerRequest: 10,
            forceStart: false,
            startUrl: '',
            processUrl: '',
            isDone: false,
            isGeneration: false,
            inProgress: false,
            percentage: 0,
            dotCount: 12,
            currentFiles: 0,
            template: 'Amasty_JetTheme/image-optimization',
            errorsList: null
        },

        initObservable: function () {
            this._super()
                .observe([
                    'inProgress',
                    'isGeneration',
                    'isDone',
                    'filesCount',
                    'filesPerRequest',
                    'part',
                    'percentage',
                    'dotCount',
                    'currentFiles',
                    'errorsList'
                ]);

            return this;
        },

        start: function () {
            this.handleErrors();

            if (this.inProgress() || this.isGeneration()) {
                return;
            }

            this.isGeneration(true);
            this.isDone(false);

            $.ajax({
                url: this.startUrl,
                type: 'GET',
                success: function (data) {
                    if (_.has(data, 'errors') && data.errors.length) {
                        this.errorsList(data.errors);

                        return false;
                    }

                    this.filesCount(parseInt(data.filesCount));
                    this.filesPerRequest(parseInt(data.filesPerRequest));
                    this.parts = Math.round(this.filesCount() / this.filesPerRequest()) + 1;
                    this.part(0);
                    this.inProgress(true);
                    this.isGeneration(false);
                    this.optimizeFiles();
                }.bind(this)
            });
        },

        /**
         * Reset ajax states
         */
        handleErrors: function () {
            if (this.errorsList()) {
                this.isGeneration(false);
                this.inProgress(false);
                this.errorsList(null);
            }
        },

        getPercentage: function () {
            if (this.filesCount() === 0) {
                return 0;
            }

            return Math.ceil(this.currentFiles() / this.filesCount() * 100);
        },

        getFilePerRequest: function () {
            var currentFiles = this.part() * this.filesPerRequest();

            if (currentFiles > this.filesCount()) {
                return this.filesCount();
            }

            return currentFiles;
        },

        optimizeFiles: function () {
            $.ajax({
                url: this.processUrl,
                data: {
                    limit: this.filesPerRequest
                },
                type: 'GET',
                success: function () {
                    this.part(this.part() + 1);
                    this.currentFiles(this.getFilePerRequest());
                    this.percentage(this.getPercentage());

                    if (this.part() < this.parts) {
                        this.optimizeFiles();
                    } else {
                        this.inProgress(false);
                        this.isDone(true);
                    }
                }.bind(this)
            });
        }
    });
});
