(function ($) {
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
