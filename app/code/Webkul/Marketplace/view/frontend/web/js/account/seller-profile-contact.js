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
        $.widget('mage.sellerProfileContact', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                self = this;
                var askDataForm = $('#ask-form');
                askDataForm.mage('validation', {});

                $('body').append($('#wk-mp-ask-data'));
                $('.askque').click(function() {
                    $('#ask-form input,#ask-form textarea').removeClass('mage-error');
                    $('.page-wrapper').css('opacity','0.4');
                    $('.wk-mp-model-popup').addClass('_show');
                    $('#wk-mp-ask-data').show();
                });
                $(".write-review").click(function() {
                    $('html,body').animate({
                        scrollTop: $("#customer-reviews").offset().top},
                        'slow');
                });
                $('.wk-close').click(function() {
                    $('.page-wrapper').css('opacity','1');
                    $('#resetbtn').trigger('click');
                    $('#wk-mp-ask-data').hide();
                    $('#ask-form .validation-failed').each(function() {
                        $(this).removeClass('validation-failed');
                    });
                    $('#ask-form .validation-advice').each(function() {
                        $(this).remove();
                    });
                });
                $('#askbtn').click(function() {
                    if (askDataForm.valid()!=false) {
                        var thisthis = $(this);
                        if (thisthis.hasClass("clickask")) {
                            if (self.options.captchenable) {
                                var total = parseInt($('#wk-mp-captchalable1').text()) + 
                                parseInt($('#wk-mp-captchalable2').text());
                                var wk_mp_captcha = $('#wk-mp-captcha').val();
                                if (total != wk_mp_captcha) {
                                    $('#wk-mp-captchalable1').text(Math.floor((Math.random()*10)+1));
                                    $('#wk-mp-captchalable2').text(Math.floor((Math.random()*100)+1));
                                    $('#wk-mp-captcha').val('');
                                    $('#wk-mp-captcha').addClass('mage-error');
                                    $(this).addClass('mage-error');
                                    $('#ask_form .errormail').text(self.options.wrongNumber)
                                    .slideDown('slow').delay(2000).slideUp('slow');
                                } else {
                                    thisthis.removeClass('clickask');
                                    $('#wk-mp-ask-data').addClass('mail-procss');
                                    $.ajax({
                                        url: self.options.mailUrl,
                                        data:$('#ask-form').serialize(),
                                        type:'post',
                                        dataType:'json',
                                        success:function(d) {
                                            thisthis.addClass('clickask');
                                            $('#wk-mp-ask-data').removeClass('mail-procss')
                                            alert(self.options.mailSent);
                                            $('.wk-close,#resetbtn').trigger('click');
                                        }
                                    });
                                }
                            } else {
                                thisthis.removeClass('clickask');
                                    $('#wk-mp-ask-data').addClass('mail-procss');
                                    $.ajax({
                                        url: self.options.mailUrl,
                                        data:$('#ask-form').serialize(),
                                        type:'post',
                                        dataType:'json',
                                        success:function(d) {
                                            thisthis.addClass('clickask');
                                            $('#wk-mp-ask-data').removeClass('mail-procss')
                                            alert(self.options.mailSent);
                                            $('.wk-close,#resetbtn').trigger('click');
                                        }
                                    });
                                }
                        }
                        return false;
                    }
                });

            }
        });
    return $.mage.sellerProfileContact;
});