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
        $.widget('mage.verifyShop', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                self = this;
                $("input[type='file']").attr('form','customer_form');
                // $("#marketplace_profileurl").maxlength({maxChars: 10});
                $("#marketplace_profileurl").keyup(function(){
                    $(this).val($(this).val().replace(/[^a-z^A-Z^0-9\.\-]/g,""));
                    var $this = $(this);
                    var val = $this.val();
                    var valLength = val.length;
                    var maxCount = 50;
                    if(valLength>maxCount){
                        $this.val($this.val().substring(0,maxCount));
                    }
                    
                });
                $("#marketplace_profileurl").on('change',function(){
                    $('.success_profile_msg').hide();
                    $('.error_profile_msg').hide();
                    var profileurl = $(this).val();
                    var selfThis = $(this);
                    if(profileurl != ''){
                        $.ajax({
                            url: self.options.ajaxCheckUrl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                profileurl:profileurl
                            },
                        complete: function(transport) {
                                if(transport.responseJSON == 0){
                                    selfThis.after($('.success_profile_msg').show());
                                }else{
                                    selfThis.after($('.error_profile_msg').show());
                                }  
                            },
                            error: function (xhr, status, errorThrown) {
                                console.log('Error happens. Try again.');
                            }
                        });
                    }
                });
            }
        });

    return $.mage.verifyShop;
});