define(['jquery'], function ($) {
    'use strict';

    return function () {
        $(document).on('click', '[data-role=mobile-nav-close]', function () {
            $('html').removeClass('nav-before-open nav-open');
        });
    };
});