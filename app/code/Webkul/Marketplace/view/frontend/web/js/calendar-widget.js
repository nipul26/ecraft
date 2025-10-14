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
    'jquery',
    'jquery/ui'
    ], function($){
        $.widget('mage.calendarWidget', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                self = this;
                var dayNames, dayNamesMin, monthNames, monthNamesShort, enUS;
                dayNames = self.options.dayNames;
                dayNamesMin = self.options.dayNamesMin;
                monthNames = self.options.monthNames;
                monthNamesShort = self.options.monthNamesShort;
                enUS = self.options.enUS;
                var config = {
                        dayNames,
                        dayNamesMin,
                        monthNames,
                        monthNamesShort
                    };
                $.extend(true, $, {
                    calendarConfig: config
                });
                enUS = {enUS}; // en_US locale reference
            }
        });

    return $.mage.calendarWidget;
});