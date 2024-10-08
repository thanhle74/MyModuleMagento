define([
    "jquery"
], function ($) {
    "use strict";
    return function (config) {
        $(document).ready(function () {
            $('.ajax-star-icon').each(function(){
                const _this = $(this);
                const numFill = parseInt( _this.data('fill') );
                _this.html(''); //clear start;
                for (let index = 0; index < 5; index++) {
                    if(index < numFill){
                        _this.append($('<span class="icon-star-fill"></span>'));

                    }else{
                        _this.append($('<span class="icon-star"></span>'))
                    }
                }
            })
        });
    };
});
