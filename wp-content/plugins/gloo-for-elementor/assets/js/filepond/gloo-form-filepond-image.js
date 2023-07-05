jQuery(function ($) {

    // $(document).on('change', 'input.gloo-image-upload-ui-input', function (event) {
        
    //     if(event.target.files.length >= 1)
    //         var uploadedImg = URL.createObjectURL(event.target.files[0]);
    //     else
    //         var uploadedImg = '';
        
            
    //     $(this).closest(".gloo-image-upload-ui-wrapper").attr('data-background-image', uploadedImg);
    //     var elementPath = fullPath(event.target),
    //         styleElem = document.head.appendChild(document.createElement("style"));
    //     $(this).parent('div').attr('data-input-id', elementPath);
    //     styleElem.innerHTML = "div[data-input-id='"+elementPath +"'] label:before {background-image: url(" + uploadedImg + ");}";
    //     // styleElem.innerHTML = elementPath + ":before {background-image: url(" + uploadedImg + ");}";
        
        
    // });

    // function fullPath(el) {
    //     var names = [];
    //     while (el.parentNode) {
    //         if (el.id) {
    //             names.unshift('#' + el.id);
    //             break;
    //         } else {
    //             if (el == el.ownerDocument.documentElement) names.unshift(el.tagName);
    //             else {
    //                 for (var c = 1, e = el; e.previousElementSibling; e = e.previousElementSibling, c++) ;
    //                 names.unshift(el.tagName + ":nth-child(" + c + ")");
    //             }
    //             el = el.parentNode;
    //         }
    //     }
    //     return names.join(" > ");
    // }

});

(function($) {

    jQuery( window ).on( 'elementor/frontend/init', () => {
        const addHandler = ( $element ) => {
            var fields = $element.find('input.gloo-filepond-upload');
            // console.log(fields);
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
            );

            if( typeof fields != 'undefined' ) {
                fields.each(function( key, val) {
                    
                    var element = '#'+this.id;
                    var uploads = $(this).parent('.elementor-field-type-upload').data('gloo-uploads');
                    var base_url = $(this).parent('.elementor-field-type-upload').data('filepond-url');
                    var settings = $(this).parent('.elementor-field-type-upload').data('config');

                    if(typeof base_url != 'undefined' && base_url != '' ) {
    
                        pond = FilePond.create(
                            document.querySelector(element), 
                            settings
                        );

                        if( typeof uploads != 'undefined' && uploads !='') {
                            var files = [];
                            uploads.forEach(function (item) {
                                var file = { source: ''+item.id+'', options: { type: "local"} };
                                files.push(file);
                            });
        
                            pond.setOptions({
                                server: {
                                    load: (source, load, error, progress, abort, headers) => {
                                        fetch(base_url+'/wp-json/gloo-uploads/v1/action/?media_id='+source,{
                                            method: 'GET',
                                            credentials: 'same-origin',
                                        })
                                        .then(response => response.json())
                                        .then(  
                                            src => {
                                                fetch(src)
                                                .then(
                                                    response => {
                                                        response.blob().then(function(myBlob) {
                                                        //console.log(myBlob);
                                                        load(myBlob);
        
                                                        return {
                                                            abort: () => {
                                                                // User tapped cancel, abort our ongoing actions here
                                            
                                                                // Let FilePond know the request has been cancelled
                                                                abort();
                                                            },
                                                        };
                                                    });
                                                });
                                            }
                                        );
                                    },
                                },
                                files: files,
                            });
                        }
                    }
                });

                /* elementor form action after submit */
                $(document).on("submit_success", function(event, formID) {
                    console.log(event);
                    console.log(formID);
                });
            }
        };

        elementorFrontend.hooks.addAction( 'frontend/element_ready/form.default', addHandler );
    });
})(jQuery);
