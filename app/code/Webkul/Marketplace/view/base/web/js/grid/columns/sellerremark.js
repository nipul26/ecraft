/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    './column',
    'jquery',
    'mage/template',
    'text!Webkul_Marketplace/templates/grid/cells/sellerremark.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, remarkPreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        gethtml: function (row) {
            return row[this.index + '_html'];
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },
        getTitle: function (row) {
            return row[this.index + '_title']
        },
        getCloselabel: function (row) {
            return row[this.index + '_close']
        },
        getData: function (row) {
            return row[this.index + '_data']
        },
        preview: function (row) {
            var modalHtml = mageTemplate(
                remarkPreviewTemplate,
                {
                    data: this.getData(row),
                }
            );
            var previewPopup = $('<div></div>').html(modalHtml);
            if (previewPopup.find(".wk-date").html()) {
                previewPopup.modal({
                    title: this.getTitle(row),
                    innerScroll: true,
                    modalClass: '_remark-box',
                    buttons: []}).trigger('openModal');
            }
        },
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});
