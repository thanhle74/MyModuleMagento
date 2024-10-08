/*
* Init source video introduction meal plan
*/
require([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($, modal ) {
    $(document).ready(function (){

        const modalVideo = $("#modal-dish");

        const options = {
            type: 'popup',
            responsive: true,
            modalClass: 'mealplan-video-modal',
        };

        const popup = modal(options, modalVideo);

        // click introduce video
        $(document).on('click', '.ajax-instruction-video',function(){
            // get src video on button
            const srcVideo = $(this).data("src");
            // init src video
            $('.mealplan-video-modal #modal-dish .container-video-mealplan .frame-wrapper iframe').attr('src', srcVideo);
            modalVideo.modal('openModal');
        });

        $(document).on('click', '.mealplan-video-modal .action-close',function(){
            // clear src
            $('#modal-dish .container-video-mealplan .frame-wrapper iframe').attr('src', "");
        });

    });
});
