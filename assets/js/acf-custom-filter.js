(function ($) {
    function getNewsletterPostId() {
        var postId = $("#post_ID").val();

        if (postId) {
            return postId.toString();
        }

        return new URLSearchParams(window.location.search).get("post");
    }

    $.ajaxPrefilter(function (options) {
        var postId = getNewsletterPostId();

        if (!postId || !options.url || options.url.indexOf("admin-ajax.php") === -1 || options.url.indexOf("action=oembed-cache") === -1) {
            return;
        }

        var requestPostId = new URL(options.url, window.location.origin).searchParams.get("post");

        if (requestPostId !== postId) {
            return;
        }

        options.beforeSend = function () {
            return false;
        };
    });

    $(document).ready(function () {
        $(document).on("change", '.acf-relationship .filters select[data-filter="taxonomy"]', function () {
            var $select = $(this);

            $select.find("optgroup").each(function () {
                if ($(this).attr("label") !== "Categoria") {
                    $(this).remove();
                }
            });
        });

        $('.acf-relationship .filters select[data-filter="taxonomy"]').trigger("change");
    });
})(jQuery);
