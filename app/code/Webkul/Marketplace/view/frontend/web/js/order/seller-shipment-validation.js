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
    "Magento_Ui/js/modal/alert",
    "prototype",
    'jquery/ui'
    ], function($, alert){
        $.widget('mage.shipmentValidation', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                 self = this;
                 if (jQuery('#wk-mp-send-email').length) {
                    jQuery('#wk-mp-send-email').on('change', shipmentSendEmail);
                    shipmentSendEmail();
                }
                function shipmentSendEmail() {
                    if (jQuery('#wk-mp-send-email').prop('checked') == true) {
                        jQuery('#wk-mp-notify-customer').prop('disabled', false);
                    } else {
                        jQuery('#wk-mp-notify-customer').prop('checked', false);
                        jQuery('#wk-mp-notify-customer').prop('disabled', true);
                    }
                }
                jQuery('#wk_mp_submit_shipment').on('click', submitSellerShipment);
            
                function submitSellerShipment() {
                    if (!validShipmentItemsQty()) {
                        alert({
                            content: self.options.invalidQtyAlert
                        });
                        return false;
                    }
                }
                function validShipmentItemsQty() {
                    var valid = true;
                    $('.wk-qty-input').each(function(shipmentItem) {
                        var orderQty = parseFloat($(this).attr('data-orig'));
                        var val = parseFloat($(this).val());                                
                        if (orderQty == "" || isNaN(val) || val < 0) {
                            valid = false;
                        }
                        if(val > orderQty) {
                            valid = false;
                        }
                    });
                    return valid;
                }
                window.submitSellerShipment = submitSellerShipment;
                window.validShipmentItemsQty = validShipmentItemsQty;
                window.shipmentSendEmail = shipmentSendEmail;
            }
        });

    return $.mage.shipmentValidation;
});