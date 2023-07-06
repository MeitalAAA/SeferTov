(function ($, window, document) {
    'use strict';
    // execute when the DOM is ready
    $(document).ready(function () {
        
        // js 'change' event triggered on the wporg_field form field
        $('#source_type').on('change', function () {
            
            var source_type = $(this).val();
            if(typeof source_type !== "undefined" ) {

                if(source_type == 'google_spreadsheet') {
                    $('#spreadsheet-field-id').show();
                } else {
                    $('#spreadsheet-field-id').hide();
                }
            }
        });

        var $sync_now = $('#js-sync-data');

        $sync_now.on('click', function(event){
            
            event.preventDefault();
            var post_ID = $('#post_ID').val();

            $sync_now.attr('disabled',true);

            $.ajax({
                type: 'POST',
                url: data_source_ajax.ajax_url,
                data: {
                    action: 'sync_data_source',  
                    'source_id': post_ID             // POST data, action
                },
                beforeSend: function() {
                    $('.gloo-loader').show();
                },
                success: function(response) {
                    console.log(response);
                    $('.gloo-loader').hide();
                    $sync_now.attr('disabled',false);

                    if (response.status == true) {
                        $sync_now.after('<p style="color: #008000;">'+response.message+'</p>');
                    } else {
                        $sync_now.after('<p style="color: #FF0000;">'+response.message+'</p>');
                    }
                },
                error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                },
            });

        });

        /* sheename toggled */
        $('#js-enable-spreadsheet-name').on('change', function(event) {
            event.preventDefault();
            
            if($(this).is(':checked')){
                $('#js-sheet-name').show();
            } else {
                $('#js-sheet-name').hide();
            }
        });
    });
}(jQuery, window, document));