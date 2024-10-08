require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'owlcarousel'
], function($,modal, urlBuilder) {
    $(document).ready(function (){
        let options = {
            type: 'popup',
            modalClass: 'mealplan-modal',
            responsive: true,
        };
        let healthlabViewModal = $('#healthlab-view-modal');
        let popup = modal(options, healthlabViewModal);

        healthlabViewModal.modal('openModal');

        $(document).on('click', '.btn-mealplan-view-close',function(){
            healthlabViewModal.modal('closeModal');
        });

        $('#submit-button-view-service').on('click', function(){
            $.ajax({
                url: urlBuilder.build('meal/ajax/sendmail'),
                type: 'POST',
                data: {
                    name : $('#view-firstname').val(),
                    email : $('#view-email').val(),
                    content: $('#view-note').val()
                },
                dataType: 'json',
                showLoader: true,
                success: function(response) {
                    if(response.success)
                    {
                        healthlabViewModal.modal('closeModal');
                        $('#sendmail-success-modal').modal('openModal');
                    }else {
                        $('#view-error-message').text(response.message).show();
                    }

                    $("body").trigger("processStop");
                },
                error: function(xhr, status, error) {
                    $('#view-error-message').text(error).show();
                }
            });
        });
    });

    $(".owl-carousel-meal-plan").owlCarousel({
        autoPlay: false, //Set AutoPlay to 3 seconds
        loop: false,
        items: 1,
        touchDrag: false,
        mouseDrag: false,
    });

    $(document).on('click', '.day-item',function(){
        const _this = $(this);
        const index = _this.data('slide');
        $('.owl-carousel-meal-plan').trigger('to.owl.carousel', index);
        $('.day-item').removeClass('day-active');
        _this.addClass('day-active');
    });

    $(document).on('click', '.ajax-changetab',function(){
        const _this = $(this);
        const targetTabId = _this.data('tab');
        const targetDayId = _this.data('day');
        $(`${targetDayId} .tab-pill`).removeClass('tab-active');
        $(`${targetDayId} .meal-title`).removeClass('meal-active');

        $(`${targetDayId} ${targetTabId}`).addClass('tab-active');
        _this.addClass('meal-active');
    });
});
