jQuery(document).ready(function () {
    var ajaxSearchTimeOut;
    var holder = jQuery(".infospice-search-form-keyword .infospice-search-complete");

    jQuery(".infospice-search-form-keyword input").on("keyup keypress", function () {
        var inp = jQuery(this);
        clearTimeout(ajaxSearchTimeOut);
        ajaxSearchTimeOut = setTimeout(function () {
            query =  inp.val();
            jQuery.ajax({
                data: {ajax_search: "Y", q: query, how: "r"},
                url : "",
                method: "POST"
            }).done(function (result) {
                result = jQuery("<div>" + result + "</div>");
                var count = jQuery(".infospice-search-form-keyword .infospice-search-complete-holder li", result).length;
                if (count > 0) {
                    holder.show();
                    holder.html(jQuery(".infospice-search-form-keyword .infospice-search-complete", result).html());
                }
                else {
                    holder.hide();
                }
            }).error(function (result) {

            });
        }, 150);
    });

    jQuery(document).click(function(event) {
        if (jQuery(event.target).closest(".infospice-search-form-keyword").length) {
            var count = jQuery(".infospice-search-form-keyword .infospice-search-complete-holder li").length;
            if (count > 0) {
                jQuery(holder).show();
            }
            return;
        }
        jQuery(holder).hide();
        event.stopPropagation();
    });
});