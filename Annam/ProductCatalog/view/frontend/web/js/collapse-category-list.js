define([
    'jquery'
], function($) {
    'use strict';
    return function(config) {
        $(document).ready(function() {
            $('.index-category-list').each(function() {
                $(this).css('height', $(this).height());
                $(this).addClass('collapsed');
            });

            $('.button-collapse').click(function (){
                $(this).closest('.index-category-list').toggleClass('collapsed');
            })
        });
    }
});
