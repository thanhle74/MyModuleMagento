define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "mage/url"
], function ($, modal, urlBuilder) {
    "use strict";
    return function (config) {
        $(document).ready(function () {
            let url = config.url;
            let isLoggedInUrl = config.isLoggedInUrl;

            let options = {
                type: 'popup',
                responsive: true,
                title: 'Sign in',
                modalClass: 'custom-modal',
                buttons: [{
                    text: $.mage.__('Ok'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };
            let modalLoginElement = $('#modal-login-wishlist')
            let popup = modal(options, modalLoginElement);

            let bntWishlist = $('.btn-wishlist');
            bntWishlist.click(function (){
                $.ajax({
                    url: isLoggedInUrl,
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    success: function(response) {
                        if(response.success)
                        {
                            addToWishlist();
                        }else {
                            showLoginPopup();
                        }

                        $("body").trigger("processStop");
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });

            function showLoginPopup() {
                modalLoginElement.modal('openModal');
            }

            function addToWishlist() {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    success: function(response) {
                        setCount();
                        $("body").trigger("processStop");
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            function setCount()
            {
                let ajaxUrl = urlBuilder.build('healthlab/wishlist/count');
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    success: function(response) {
                        if(response.success)
                        {
                            $('.tooltip-qty').text(response.count)
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
