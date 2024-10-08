define([
    'jquery'
], function($) {
    'use strict';
    return function(config) {
        $(document).ready(function() {
            const url = config.url;
            const searchInput = $('.healthlab-index-index #index-search');
            const urlCurrent = new URL(window.location.href);
            const searchWrapper = $('.healthlab-index-index #healthlab-search-wrapper');
            const TIME_DEBOUCE = 600; //ms

            const urlPage = new URL(window.location.href);
            const keywordInit = urlPage.searchParams.get('keyword');
            searchInput.val(keywordInit);
            getAjax(keywordInit);

            searchInput.on("change keyup paste", debounce(function() {
                const keyword = $(this).val();
                urlCurrent.searchParams.set('keyword', keyword);
                window.history.replaceState({}, '', urlCurrent);
                getAjax(keyword);
                searchWrapper.addClass('show');
            }, TIME_DEBOUCE));

            // show options
            searchInput.on("focus", function() {
                searchWrapper.addClass('show');
            });

            // hide options
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.healthlab-search-wrapper').length
                    && !$(event.target).closest('.healthlab-index-index #index-search').length) {
                    // Click occurred outside the .healthlab-search-wrapper element and its children
                    searchWrapper.removeClass('show');
                }
            });

            function getAjax(keyword) {
                if(searchInput.length)
                {
                    if(keyword === null || keyword.trim() === '')
                    {
                        keyword = 'all';
                    }
                }
                try {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            'keyword': keyword
                        },
                        dataType: 'json',
                        showLoader: true,
                    }).done(function(data) {
                        searchWrapper.html(data.output);
                        $('body').trigger('processStop');
                        return true;
                    })
                } catch (e) {
                    console.log("Some thing error in search options!");
                }
            }

            function debounce(func, delay) {
                let timeoutId;

                return function() {
                  const context = this;
                  const args = arguments;

                  clearTimeout(timeoutId);

                  timeoutId = setTimeout(() => {
                    func.apply(context, args);
                  }, delay);
                };
            }
        });
    }
});
