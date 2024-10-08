define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "mage/translate",
    "mage/calendar"
], function ($,modal, $t) {
    "use strict";
    return function (config) {
        $(document).ready(function () {
            let url = config.url;
            let modalCreateElement = $('#modal-create-wishlist');
            let modalLoginElement = $('#modal-login-wishlist');
            let options = {
                type: 'popup',
                responsive: true,
                title: $t('Create your account'),
                modalClass: 'custom-modal',
            };
            let popup = modal(options, modalCreateElement);

            $('#create-dob').calendar({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                currentText: $t('Go Today'),
                closeText: $t('Close'),
                showWeek: true,
                yearRange: '1900:2100'
            });


            $('#submit-button-create').on('click', function() {
                let email = $('#create-email');
                let firstname = $('#create-firstname');
                let lastname = $('#create-lastname');
                let dob = $('#create-dob');
                let password = $('#create-password');
                let passwordConfirm = $('#create-password-confirm');
                let messagePasswordConfirm = $('#message-password-confirm')
                let valEmail = email.val();
                let valFirstname = firstname.val();
                let valLastname = lastname.val();
                let valDob = dob.val();
                let valPassword = password.val();
                let valPasswordConfirm = passwordConfirm.val();

                if(email.length && password.length && firstname.length && lastname.length && dob.length)
                {
                    if (valPassword === valPasswordConfirm) {
                        messagePasswordConfirm.html('Matching').css('color', 'green');
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                email : valEmail,
                                password : valPassword,
                                firstname: valFirstname,
                                lastname: valLastname,
                                dob: valDob
                            },
                            dataType: 'json',
                            showLoader: true,
                            success: function(response) {
                                if(response.success)
                                {
                                    modalCreateElement.modal('closeModal');
                                    modalLoginElement.modal('openModal');
                                }else {
                                    $('#error-message-create').text(response.message).show();
                                }

                                $("body").trigger("processStop");
                            },
                            error: function(xhr, status, error) {
                                $('#error-message-create').text(error).show();
                            }
                        });
                    } else
                    {
                        messagePasswordConfirm.html('Not Matching').css('color', 'red');
                    }
                }else {
                    $('#error-message-create').text('Please fill in the field completely').show();
                }
            })
        });
    };
});
