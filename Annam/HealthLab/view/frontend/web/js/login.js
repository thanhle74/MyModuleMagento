define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "mage/translate",
], function ($,modal, $t) {
    "use strict";
    return function (config) {
        $(document).ready(function () {
            let url = config.url;
            let modalCreateElement = $('#modal-create-wishlist');
            let modalSuccessElement = $('#modal-success');
            let modalLoginElement = $('#modal-login-wishlist');
            let bntCreateElement = $('#submit-create');
            let options = {
                type: 'popup',
                responsive: true,
                title: $t('successful'),
                modalClass: 'modal-success-wrapper'
            };

            let popup = modal(options, modalSuccessElement);

            bntCreateElement.on('click', function(){
                modalLoginElement.modal('closeModal');
                modalCreateElement.modal('openModal');
            })

            $('#submit-button').on('click', function() {
                let username = $('#username');
                let password = $('#password');
                let valUsername = username.val();
                let ValPassword = password.val();

                if(valUsername.length && password.length)
                {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            username : valUsername,
                            password : ValPassword
                        },
                        dataType: 'json',
                        showLoader: true,
                        success: function(response) {
                            if(response.success)
                            {
                                modalLoginElement.modal('closeModal');
                                modalSuccessElement.modal('openModal');
                            }else {
                                $('#error-message').text(response.message).show();
                            }

                            $("body").trigger("processStop");
                        },
                        error: function(xhr, status, error) {
                            $('#error-message').text(error).show();
                        }
                    });
                }else {
                    $('#error-message').text('Please fill in the field completely').show();
                }
            });

            $(document).on('click' , '.ajax-close-success-modal',function(){
                modalSuccessElement.modal('closeModal');
            })
        });
    };
});
