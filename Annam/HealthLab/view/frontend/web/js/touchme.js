
define([
    "jquery",
    "Magento_Ui/js/modal/modal"
], function ($,modal) {
    "use strict";
    return function () {
        $(document).ready(function() {
            let modalContent = $('#modal-touchme');
            let options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                buttons: [],
                modalClass: 'touchme-modal-wapper'
            };

            let popup = modal(options, modalContent);
            modalContent.modal('openModal');

            $(document).on('click', '.btn-touchme',function(){
                modalContent.modal('closeModal');
            })
        });
    };
});
