require([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "mage/url"
],function($, modal, urlBuilder) {
    $(document).ready(function (){
        let options = {
            type: 'popup',
            responsive: true,
            modalClass: 'mealplan-modal',
        };

        let informationModal = $('#healthlab-information-modal');
        let popup = modal(options, informationModal);

        $('.healthlab__footer-button-email').click(function (){
            informationModal.modal('openModal');
        });

        $(document).on('click', '.btn-mealplan-close',function(){
            informationModal.modal('closeModal');
        });

        $('#submit-button-information-service').on('click', function(){
            $.ajax({
                url: urlBuilder.build('healthlab/popup/information'),
                type: 'POST',
                data: {
                    name : $('#information-firstname').val(),
                    email : $('#information-email').val(),
                },
                dataType: 'json',
                showLoader: true,
                success: function(response) {
                    if(response.success)
                    {
                        informationModal.modal('closeModal');
                        $('#sendmail-success-modal').modal('openModal');
                    }else {
                        $('#information-error-message').text(response.message).show();
                    }

                    $("body").trigger("processStop");
                },
                error: function(xhr, status, error) {
                    $('#information-error-message').text(error).show();
                }
            });
        });
    });
});
