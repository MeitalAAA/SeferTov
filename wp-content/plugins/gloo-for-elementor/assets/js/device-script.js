(function($) {

    jQuery( window ).on( 'elementor/frontend/init', () => {
        
        const addHandler = ( $element ) => {

            if ( $element.find( '.gloo-source-gallery' ) ) {

                var options = {
                    fade: false,
                    autoplay: true,
                    autoplaySpeed: 7000,
                    autoplay: true,
                    infinite: true,
                    dots: true,
                    speed: 500,
                    arrows: true,
                    prevArrow: '<a class="slide-arrow prev-arrow"></a>',
                    nextArrow: '<a class="slide-arrow next-arrow"></a>'
                };
             
                var settings =  $element.data( 'settings' );
                
                if( 'undefined' != typeof settings ) {
    
                    if('undefined' != typeof settings.gloo_device_gallery_transition ) {
                        
                        if(settings.gloo_device_gallery_transition == 'fade') {
                            options.fade = true;
                        }
                    }

                    if('undefined' != typeof settings.gloo_device_gallery_autoplay ) {

                        if(settings.gloo_device_gallery_autoplay != 'yes') {
                            options.autoplay = false;
                            options.autoplaySpeed = settings.gloo_device_gallery_autoplay_speed;
                        }    
                    }

                    if('undefined' != typeof settings.gloo_device_gallery_infinite ) {

                        if(settings.gloo_device_gallery_infinite != 'yes') {
                            options.infinite = false;
                        }
                    }

                    if('undefined' != typeof settings.gloo_device_gallery_transition_speed ) {
                        options.speed = settings.gloo_device_gallery_transition_speed;
                    }                    
                }               
                
                $('.slick-wrapper').not('.slick-initialized').slick(options);
            }

        };

        elementorFrontend.hooks.addAction( 'frontend/element_ready/gloo_device_widget.default', addHandler );
    });
})(jQuery);
