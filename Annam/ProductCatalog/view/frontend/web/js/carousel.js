define([
    'jquery','owlcarousel'
], function($) {
    'use strict';
    return function() {
        $(document).ready(function() {

            $(document).on('click', 'button.ajax-collapse', function(){
                $('.collapse-box').toggleClass('collapse-closed');
            });

            const owlCarousel = $(".owl-carousel-relation");

            owlCarousel.owlCarousel({
                loop: false,
                items: 4,
                margin : 20,
                touchDrag: true,
                mouseDrag: true,
            });

            $(".owl-prev").click(function () {
                // slide prev
                owlCarousel.trigger("prev.owl.carousel");
            });

            $(".owl-next").click(function () {
                // slide prev
                owlCarousel.trigger("next.owl.carousel");
            });

        });
    }
});
