define([
    'jquery'
], function($) {
    'use strict';
    return function(config) {
        $(document).ready(function() {
            let toolBar = $('.healthlab_search-results-view #healthlab-toolbar');
            let sortBy = $('.healthlab_search-results-view .healthlab-sort-by');
            let search = $('.healthlab_search-results-view .healthlab-category-search input');
            let url = config.url;
            let data = {
                id: config.id,
                type: toolBar.val(),
                sort: sortBy.data('value'),
                keyword: search.val()
            }

            getAjax();

            toolBar.change(function() {
                data.type = $(this).val();
                getAjax();
            });

            sortBy.click(function () {
                $(this).data(
                    "value",
                    $(this).data("value") === "asc" ? "desc" : "asc"
                );
                sortBy.toggleClass("current");
                data.sort = $(this).data("value");
                getAjax();
            });
            search.on("change keyup paste", function() {

                data.keyword = $(this).val();
                getAjax();
            });

            function getAjax() {
                if(search.length > 0 && sortBy.length >0 && toolBar.length > 0)
                {
                    if(search.val().trim() === '')
                    {
                        data.keyword = 'all';
                    }
                }
                try {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        showLoader: true,
                    }).done(function(data) {
                        $('#healthlab-category-products').html(data.output);
                        $('body').trigger('processStop');
                        return true;
                    })
                } catch (e) {}
            }
        });
    }
});
