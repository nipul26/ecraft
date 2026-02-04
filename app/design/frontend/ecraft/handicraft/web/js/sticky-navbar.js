require(['jquery'], function ($) {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.nav-sections').addClass('is-sticky');
        } else {
            $('.nav-sections').removeClass('is-sticky');
        }
    });
});