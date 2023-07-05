jQuery( function( $ ) {

    $(document).on('click','.gloo-clickable-item', function(){
        var clickable_item = $(this),
        clickable_url = clickable_item.data('gloo-item-clickable'),
        target = clickable_item.data('gloo-item-clickable-blank');
       
        if(clickable_url) {
             // smooth scroll
            if ( clickable_url.indexOf('#') !== -1 ) {
                var hash = clickable_url.substring(clickable_url.indexOf('#')); // '#foo'

                $( 'html, body' ).animate( {
                    scrollTop: $( hash ).offset().top
                    }, 800, function() {
                    window.location.hash = hash;
                });

                return true;
            }

            window.open( clickable_url, target );
            return false;
        }
    });  
});
