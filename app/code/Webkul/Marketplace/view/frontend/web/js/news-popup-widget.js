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
    'jquery/ui',
    'mage/url'
    ], function($, ui, urlBuilder){
        $.widget('mage.newsPopupWidget', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
             _create: function() {
                 self = this;
                $(".wk-news-popup .wk-close").click(function () {
                    $(this).parents(".wk-news-popup").remove();
                });
                $(".wk-mark-read-news").click(function () {
                    $('body').trigger('processStart');
                    let publishedId = $(this).data("id");
                    $(this).parents(".wk-news-popup").remove();
                    $.ajax({
                        url: urlBuilder.build("marketplace/seller/markread"),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            publishedId:publishedId
                        },
                        complete: function(response) {
                            $('body').trigger('processStop');
                            jQuery(".wk-news-popup.dispnone:first").removeClass("dispnone");
                            if (response.responseJSON.counter > 0) {
                                let moreNews = $.mage.__('%1 More News').replace('%1', response.responseJSON.counter);
                                $(".wk-more-news-link a").text(moreNews);
                            } else {
                                $(".wk-more-news").remove();
                            }
                        },
                        error: function (xhr, status, errorThrown) {
                            console.log('Error happens. Try again.');
                        }
                    });
                });
            }
        });

    return $.mage.newsPopupWidget;
});