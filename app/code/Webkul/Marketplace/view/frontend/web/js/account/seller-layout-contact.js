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
    'jquery/ui'
    ], function($, alert){
        $.widget('mage.sellerLayoutContact', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                var self = this;
                var askDataForm = $('#tab-ask-form');
                askDataForm.mage('validation', {});
                $('#askbtntab').click(function() {
                    if (askDataForm.valid()!=false) {
                        var thisthis = $(this);
                        if (thisthis.hasClass("clickask")) {
                           
                            thisthis.removeClass('clickask');
                            $('body').trigger('processStart');
                            $.ajax({
                                url:self.options.sellerContactUrl,
                                data:$('#tab-ask-form').serialize(),
                                type:'post',
                                dataType:'json',
                                success:function(d) {
                                    thisthis.addClass('clickask');
                                    alert({
                                        title: $.mage.__('Contact Seller'),
                                        content: self.options.mailSent
                                    });
                                    $('body').trigger('processStop');
                                }
                            });
                        }
                        return false;
                    }
                });
            }
        });

    return $.mage.sellerLayoutContact;
});