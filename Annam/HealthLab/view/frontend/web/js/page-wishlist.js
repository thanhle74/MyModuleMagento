define(["jquery","mage/url"], function ($, urlBuilder) {
    "use strict";
    return function (config) {
        $(document).ready(function() {
            let wishlistSort = $('#wishlist-sort');
            let sortBy = $('.wishlist-sort-by');
            let data = {
                type: wishlistSort.val(),
                sort: sortBy.data('value')
            }

            $('.healthlab-header-icon-wishlist').click(function (){
                $('.healthlab-wishlist').toggleClass('active');
                $('body').addClass('no-scroll');
                const footer = $('.healthlab-wishlist .wishlist-footer');
                footer.html('');
                footer.append($('.healthlab__footer-button .healthlab__footer-button-print').clone());
                footer.append($('.healthlab__footer-button .healthlab__footer-button-email').clone());
                footer.append($('.healthlab__footer-button .healthlab__footer-button-language').clone());

                isLoggedIn();
            });

            $('.healthlab-wishlist .healthlab-header-box span').click(function (){
                $('.healthlab-wishlist').toggleClass('active');
                $('body').removeClass('no-scroll');
                const footer = $('.healthlab-wishlist .wishlist-footer');
                footer.html('');
            });

            wishlistSort.change(function (){
                data.type = $(this).val();
                isLoggedIn();
            });

            sortBy.click(function () {
                $(this).data("value", $(this).data("value") === "asc" ? "desc" : "asc");
                sortBy.toggleClass("current");
                data.sort = $(this).data("value");
                isLoggedIn();
            });

            function showWishlist() {
                let ajaxUrl = urlBuilder.build('healthlab/wishlist/show');
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    showLoader: true,
                    success: function(response) {
                        $('.wishlist-list').html(response.output);

                        $("body").trigger("processStop");
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            function isLoggedIn()
            {
                let ajaxUrl = urlBuilder.build('healthlab/popup/isloggedin');
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    success: function(response) {
                        if(response.success)
                        {
                            showWishlist();
                        }else {
                            $('.wishlist-list').html('<h3 class="please-login">Please Login</h3>');
                        }

                        $("body").trigger("processStop");
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    };
});
