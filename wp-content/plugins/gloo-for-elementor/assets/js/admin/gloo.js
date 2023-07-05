jQuery(document).ready(function ($) {
    $('.js-gloo-enable').on('click', function (event) {
        event.preventDefault();
        $(this).parents('.gloo-box').addClass('enabled').find('input[type="checkbox"]').prop('checked', true);

    });

    $('.js-gloo-disable').on('click', function (event) {
        event.preventDefault();
        $(this).parents('.gloo-box').addClass('enabled').find('input[type="checkbox"]').prop('checked', false);
    });

    $('.gloo-items input.flipswitch').on('change', function (event) {

        var checked = $(this).is(':checked'),
            name = $(this).attr('name');

        $.ajax({
            type: "POST",
            url: glooData.ajaxUrl,
            data: {action: "gloo_update_options", module: name, status: checked},
            success: function (response) {

            }
        });
    });


});