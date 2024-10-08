define(["jquery"], function ($) {
    "use strict";
    return function (config) {
        $('.star-icon-container').each(function() {
            const el = $(this);
            const dataNo =  parseInt(el.data('num'));
            console.log("star num: ", dataNo);
            for (let index = 1; index <= 5; index++) {
                if(index <= dataNo){
                    jQuery('<div>', {
                        class: 'start-icon-fill',
                    }).appendTo(el);
                }else{
                    jQuery('<div>', {
                        class: 'start-icon-no-fill',
                    }).appendTo(el);
                }
            }
        });
    };
});
