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
], function($, alert) {
        $.widget('mage.sellerProductInfo', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                self = this;
                var askDataForm = $('#ask-form');
                var cardType = self.options.cardType;

                askDataForm.mage('validation', {});
                var flagDataForm = $('#flag-form');
                flagDataForm.mage('validation', {});
                if (cardType == 2) {
                    $('.product.media').before($('#mp-wk-block'));
                } else {
                    $('.product-info-main').append($('#mp-wk-block'));
                }
                $('#mp-wk-block').removeClass('no-display');
                $('#mp-wk-block').addClass('wk-display-block');

                $('body').append($('#wk-mp-ask-data'));
                $('body').append($('#wk-mp-flag-data'));
                $(".wk-seller-rating-number" ).mouseover(function() {
                        $( ".wk-seller-rating" ).show();
                }).mouseout(function() {
                    $( ".wk-seller-rating" ).hide();
                    });

                $('#askque').click(function() {
                    $('#ask-form input,#ask-form textarea').removeClass('mage-error');
                    $('.page-wrapper').css('opacity','0.4');
                    $('.wk-mp-model-popup').addClass('_show');
                    $('#wk-mp-ask-data').show();
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
                                var total = parseInt($('#wk-mp-captchalable1').text()) 
                                + parseInt($('#wk-mp-captchalable2').text());
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
                                        url:self.options.sendMailUrl,
                                        data:$('#ask-form').serialize(),
                                        type:'post',
                                        dataType:'json',
                                        success:function(d) {
                                            thisthis.addClass('clickask');
                                            $('#wk-mp-ask-data').removeClass('mail-procss')
                                            alert({
                                                title: $.mage.__('Success Message'),
                                                content: $.mage.__('Your mail has been sent...'),
                                                actions: {
                                                    always: function(){
                                                        $('.wk-close,#resetbtn').trigger('click');
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
                                            $('.wk-close,#resetbtn').trigger('click');
                                        }
                                    });
                                }
                            } else {
                                thisthis.removeClass('clickask');
                                    $('#wk-mp-ask-data').addClass('mail-procss');
                                    $.ajax({
                                        url:self.options.sendMailUrl,
                                        data:$('#ask-form').serialize(),
                                        type:'post',
                                        dataType:'json',
                                        success:function(d) {
                                            thisthis.addClass('clickask');
                                            $('#wk-mp-ask-data').removeClass('mail-procss');
                                            alert({
                                                title: $.mage.__('Success Message'),
                                                content: $.mage.__('Your mail has been sent...'),
                                                actions: {
                                                    always: function(){
                                                        $('.wk-close,#resetbtn').trigger('click');
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
                                            $('.wk-close,#resetbtn').trigger('click');
                                        }
                                    });
                            }
                        }
                        return false;
                    }
                });
                $('#reportflag').click(function() {
                    $('#flag-form input,#flag-form textarea').removeClass('mage-error');
                    $('.page-wrapper').css('opacity','0.4');
                    $('.wk-mp-model-flag-popup').addClass('_show');
                    $('#wk-mp-flag-data').show();
                });
                $('.wk-product-flag-close').click(function() {
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
                                url:self.options.flagUrl,
                                data:$('#flag-form').serialize(),
                                type:'post',
                                dataType:'json',
                                success:function(response) {
                                    $('#wk-mp-flag-data').removeClass('mail-procss');
                                    thisthis.addClass('clickflag');
                                    if (response.status == true) {
                                        var messageContent = $('.wk-alert-modal-content').html();
                                        $('.wk-product-flag-close,#resetflagbtn').trigger('click');
                                        alert({
                                            title: $.mage.__('Report Product'),
                                            content: $('.wk-flag-status-content'),
                                            actions: {
                                                always: function(){
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
                                    } else {
                                        alert({
                                            title: $.mage.__('Report Product'),
                                            content: $.mage.__('Unable to send mail. Please try again later.'),
                                            clickableOverlay: false,
                                            modalClass: "mailResponse",
                                            actions: {
                                                always: function(){
                                                $('.wk-product-flag-close,#resetflagbtn').trigger('click');
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
                                    }
                                }
                            });
                        }
                        return false;
                    }
                });
            }
    })
    return $.mage.sellerProductInfo;
})