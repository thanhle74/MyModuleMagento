define(["jquery"], function ($) {
    "use strict";
    return function (config) {
        $(document).ready(function () {
            let url = config.url;
            let option = $(".meal-plan-products #dish-list-options");
            let sortBy = $(".meal-plan-products .healthlab-sort-by");
            let search = $(".meal-plan-products .healthlab-search input");
            let toolBar = $(".meal-plan-products #healthlab-toolbar");
            let data = {
                id: config.id,
                valueOption: option.val(),
                sort: sortBy.data("value"),
                type: toolBar.val(),
                keyword: search.val(),
            };

            getAjax();

            toolBar.change(function () {
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

            option.change(function () {
                data.valueOption = $(this).val();
                getAjax();
            });

            search.on("change keyup paste", function () {
                data.keyword = $(this).val();
                getAjax();
            });

            function getAjax() {
                if (
                    search.length > 0 &&
                    sortBy.length > 0 &&
                    toolBar.length > 0 &&
                    option.length > 0
                ) {
                    if (search.val().trim() === "") {
                        data.keyword = "all";
                    }
                }
                try {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: data,
                        dataType: "json",
                        showLoader: true,
                    }).done(function (data) {
                        $(
                            ".meal-plan-products #healthlab-products"
                        ).html(data.output);
                        $("body").trigger("processStop");
                        return true;
                    });
                } catch (e) {}
            }
        });
    };
});
