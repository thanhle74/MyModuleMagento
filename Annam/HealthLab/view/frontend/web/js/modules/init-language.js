define(["jquery"], function ($) {
    "use strict";
    return function (config) {
        $(document).ready(function () {
            const returnLanguage = (t) => t.includes("English") ? "EN" : "VI";

            const languageDOM = $(".switcher-language");
            // remove default swicth language
            $(".main").remove(".switcher-language");

            // re init language on footer
            $(".switch-language-wr").append(languageDOM);

            // re write character language EN, VI
            const _htmlCurrentLanguage = $(".switcher-trigger strong span").html();
            const _htmlOptionLangugage = $(".mage-dropdown-dialog ul li a").html();
            $(".switcher-trigger strong span").html( returnLanguage(_htmlCurrentLanguage) );
            $(".mage-dropdown-dialog ul li a").html( returnLanguage(_htmlOptionLangugage) );

            // Event change langugae
            $(".switch-language-btn").click(function (e) {
                e.preventDefault();
                $(".switcher-language").toggleClass("d-active");
                console.log("Event: change language.");
            });
        });
    };
});
