// jQuery( function( $ ) {
//     //Make the DIV element draggagle:
//     $( ".gloo-draggable-item" ).draggable({
//         classes: {
//             "ui-draggable": "move"
//         }
//     });
// });

(function($) {

    jQuery( window ).on( 'elementor/frontend/init', () => {

        elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $scope ) {
            if ( $scope.find( '.gloo-draggable-item' ) ) {

                $( ".gloo-draggable-item" ).draggable({
                    classes: {
                        "ui-draggable": "move"
                    }
                });
            }
        } );
    });
})(jQuery);
