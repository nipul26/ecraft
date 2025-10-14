/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_Marketplace
 * @author     Webkul Software Private Limited
 * @copyright  Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'prototype'
], function ($) {
    'use strict';

    var AssignSellerCategoryForm = new Class.create();

    AssignSellerCategoryForm.prototype = {
        initialize: function (parent) {
            this.parent = $(parent);
            this.shownElement = null;
            this.updateElement = document.getElementById('category_id');
            this.readOnly = false;
        },
    };
    return AssignSellerCategoryForm;
});
