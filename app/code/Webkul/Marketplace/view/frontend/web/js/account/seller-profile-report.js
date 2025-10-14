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
    "jquery",
    "Magento_Ui/js/modal/alert",
    "mage/mage"
    ], function($, alert){
        $.widget('mage.sellerProfileReport', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                var self = this;
                var flagDataForm = $('#flag-form');
                flagDataForm.mage('validation', {});
                $('body').append($('#wk-mp-flag-data'));
                $('.read-more').click(function() {
                    if(!$("#marketplace_sellerprofile_tab").hasClass("add-shadow")){
                        $("#marketplace_sellerprofile_tab").addClass("add-shadow");
                        setTimeout(function(){
                            $("#marketplace_sellerprofile_tab").removeClass("add-shadow")
                        }, 1000)
                    }
                    $("#tab-label-marketplace_sellerprofile_tab-title").click();
                });
                $('#reportflag').click(function() {
                    $('#flag-form input,#flag-form textarea').removeClass('mage-error');
                    $('.page-wrapper').css('opacity','0.4');
                    $('.wk-mp-model-flag-popup').addClass('_show');
                    $('#wk-mp-flag-data').show();
                });
                $('.wk-seller-flag-close').click(function() {
                    $('.page-wrapper').css('opacity','1');
                    $('#resetflagbtn').trigger('click');
                    $('#wk-mp-flag-data').hide();
                    $('#flag-form .validation-failed').each(function() {
                        $(this).removeClass('validation-failed');
                    });
                    $('#flag-form .validation-advice').each(function() {
                        $(this).remove();
                    });
                });
                $('.flag-reason').on('change',function(e) {
                if($(this).val() == "other_value") {
                    $('.wk-flag-other-reason').show();
                    $('.wk-flag-other-reason').addClass('required-entry');
                } else {
                    $('.wk-flag-other-reason').hide();
                    $('.wk-flag-other-reason').removeClass('required-entry');
                }
                });
                $('#resetflagbtn').on('click', function(e) {
                $('.wk-flag-other-reason').show();
                $('.wk-flag-other-reason').addClass('required-entry');
                });
                $('#flagbtn').click(function() {
                    if (flagDataForm.valid()!=false) {
                        var thisthis = $(this);
                        if (thisthis.hasClass("clickflag")) {
                            thisthis.removeClass('clickflag');
                            $('#wk-mp-flag-data').addClass('mail-procss');
                            $.ajax({
                                url: self.options.sellerReportUrl,
                                data:$('#flag-form').serialize(),
                                type:'post',
                                dataType:'json',
                                success:function(content) {
                                    var messageContent = $('.wk-alert-modal-content').html();
                                    thisthis.addClass('clickflag');
                                    $('#wk-mp-flag-data').removeClass('mail-procss')
                                    alert({
                                        title: $.mage.__('Report Seller'),
                                        content: $('.wk-flag-status-content'),
                                        actions: {
                                            always: function(){
                                                $('.wk-seller-flag-close,#resetflagbtn').trigger('click');
                                                $('.wk-flag-other-reason').show();
                                                $('.wk-flag-other-reason').addClass('required-entry');
                                            }
                                        },
                                        buttons: [{
                                            text: $.mage.__('Close'),
                                            class: 'action primary close',
                                            click: function () {
                                                this.closeModal(true);
                                            }
                                        }]
                                    });
                                    $('.wk-alert-modal-content').append(messageContent);
                                }
                            });
                        }
                        return false;
                    }
                });

            }
        });

    return $.mage.sellerProfileReport;
});