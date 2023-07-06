if (typeof gloo_fullPath != 'function') {
    function gloo_fullPath(el) {
        var names = [];
        while (el.parentNode) {
            if (el.id) {
                names.unshift('#' + el.id);
                break;
            } else {
                if (el == el.ownerDocument.documentElement) names.unshift(el.tagName);
                else {
                    for (var c = 1, e = el; e.previousElementSibling; e = e.previousElementSibling, c++) ;
                    names.unshift(el.tagName + ":nth-child(" + c + ")");
                }
                el = el.parentNode;
            }
        }
        return names.join(" > ");
    }
}
if (typeof openModal != 'function') {
    function openModal() {
        document.getElementById("gloo_modal").style.display = "block";
    }
}
if (typeof closeModal != 'function') {
    function closeModal(cross_click = true) {
        if(basic_croppie && typeof basic_croppie.croppie == 'function'){
            basic_croppie.croppie('destroy');
            if(current_cropped_elementPath && jQuery(current_cropped_elementPath).length >= 1 && cross_click == true){
                let container = new DataTransfer();
                jQuery(current_cropped_elementPath).prop('files', container.files);
                jQuery(current_cropped_elementPath).trigger('change');
            }
        }
        document.getElementById("gloo_modal").style.display = "none";
    }
}
var current_cropped_elementPath = '';
var current_cropped_files = [];
var basic_croppie = '';
var current_gloo_crop_options;
jQuery(document).ready(function($){
    // Cropper.noConflict();
    // $.fn.cropper.noConflict();


    // const image = document.getElementById('gloo_cropper_image_tag');
    // const cropper = new Cropper(image, {
    // aspectRatio: 16 / 9,
    // crop(event) {
    //     console.log(event.detail.x);
    //     console.log(event.detail.y);
    //     console.log(event.detail.width);
    //     console.log(event.detail.height);
    //     console.log(event.detail.rotate);
    //     console.log(event.detail.scaleX);
    //     console.log(event.detail.scaleY);
    // },
    // });

/*
    var image_cropper = $('#gloo_cropper_image_tag');

    image_cropper.cropper({
        aspectRatio: 16 / 9,
        crop: function(event) {
            console.log(event.detail.x);
            console.log(event.detail.y);
            console.log(event.detail.width);
            console.log(event.detail.height);
            console.log(event.detail.rotate);
            console.log(event.detail.scaleX);
            console.log(event.detail.scaleY);
        }
    });

    // Get the Cropper.js instance after initialized
    var cropper = image_cropper.data('cropper');*/
});

jQuery(document).ready(function($){

    // $('#gloo_cropper_image_tag').croppie();
    
    

    $(document).on('change', 'input.gloo-image-crop-input', function (event) {
        if(event.target.files.length >= 1){
            
            if(current_cropped_files.length >= 1 && event.target.files[0].name == current_cropped_files[0].name && event.target.files[0].lastModified != current_cropped_files[0].lastModified){
                event.preventDefault();
                event.stopPropagation();
                return false;
            }

            var uploadedImg = URL.createObjectURL(event.target.files[0]);
            current_cropped_elementPath = gloo_fullPath(event.target);
            current_cropped_files = event.target.files;

            gloo_crop_options = $(current_cropped_elementPath).attr('data-crop-options');
            if(typeof gloo_crop_options == 'string'){
                gloo_crop_options = JSON.parse(gloo_crop_options);
                if(typeof gloo_crop_options == 'object'){
                    openModal();
                    current_gloo_crop_options = gloo_crop_options;
                    $('.gloo_cropper_image_container_action_result').html(gloo_crop_options.button_label);
                    console.log(gloo_crop_options);
                    basic_croppie = $('#gloo_cropper_image_container').croppie({
                        // url: $('#gloo_cropper_image_tag').attr('src'),
                        // enableResize: true,
                        viewport: {
                            width: gloo_crop_options.viewport_width,
                            height: gloo_crop_options.viewport_height,
                            type: gloo_crop_options.viewport_type
                        },
                        boundary: { width: gloo_crop_options.boundary_width, height: gloo_crop_options.boundary_height },
                        enableOrientation: true,
                        enableResize: gloo_crop_options.enableResize,
                        enableExif: true,
                        // mouseWheelZoom: 'ctrl',
                        showZoomer:gloo_crop_options.showZoomer,
                        enforceBoundary: false,
                        // minZoom: -1.5000,
                        maxZoom: 2,
                    });
                    // basic_croppie.croppie('result', {
                    //     // type: 'canvas',
                    //     size:'original',
                    //     type: 'blob',
                    //     circle: true,
                    // });
                    basic_croppie.croppie('bind', {
                        url: uploadedImg,
                        zoom:0.4,
                        // input_selector: elementPath,
                        // original_files: event.target.files,
                        // url: $('#gloo_cropper_image_tag').attr('src'),
                        // points: [77,469,280,739]
                    });
                    // $(".cr-slider").attr('min', '-1.5000');
                    // console.log($(".cr-slider").attr('max'));
                }
                
            }
            
        }
    });

    $(document).on("click", ".gloo_cropper_image_container_action_result", function(e){
        if($(current_cropped_elementPath).length >= 1){
            // console.log(current_gloo_crop_options);
            result_image_size = 'viewport';
            if(typeof current_gloo_crop_options == 'object'){
                if(current_gloo_crop_options.image_size == 'custom' && typeof current_gloo_crop_options.image_size_width != 'undefined' && typeof current_gloo_crop_options.image_size_height != 'undefined'){
                    console.log(current_gloo_crop_options.image_size);
                    result_image_size = {width: current_gloo_crop_options.image_size_width, height: current_gloo_crop_options.image_size_height};
                }
                else if(current_gloo_crop_options.image_size == 'viewport' || current_gloo_crop_options.image_size == 'original'){
                    result_image_size = current_gloo_crop_options.image_size;
                }
            }
            // console.log(result_image_size);
            //on button click
            // basic_croppie.croppie('result', {format: 'jpeg', type: 'blob'}).then(function(resp) {
            basic_croppie.croppie('result', 
            {
                //     // type: 'canvas',
                    size: result_image_size,
                    type: 'blob',
                    // circle: true,
                }
            ).then(function(resp) {
                
                // old_files = document.querySelector('#form-field-field_d6c2592').files;
                // old_files = current_cropped_files;
                if(current_cropped_files.length >= 1){
                    var new_file = new File([resp], current_cropped_files[0].name, {type: current_cropped_files[0].type/*, lastModified: current_cropped_files[0].lastModified*/});
                    let container = new DataTransfer();
                    container.items.add(new_file);
                    // document.querySelector('#form-field-field_d6c2592').files = container.files;
                    // $('input#form-field-field_d6c2592')[0].files = container.files;
                    $(current_cropped_elementPath).prop('files', container.files);
                    $(current_cropped_elementPath).trigger('change');
                    
                    // var uploadedImg = URL.createObjectURL(resp);
                    // $(".image_preview").html('<img src="'+uploadedImg+'" />');
                }
                
            });
        }
        closeModal(false);
        e.preventDefault();
        e.stopPropagation();
        return false;
    });


    // $(document).on('change', 'input.gloo-image-upload-ui-input', function (event) {
    //     var uploadedImg = URL.createObjectURL(event.target.files[0]);
    //     $(this).closest(".gloo-image-upload-ui-wrapper").attr('data-background-image', uploadedImg);
    //     var elementPath = fullPath(event.target),
    //         styleElem = document.head.appendChild(document.createElement("style"));
    //     $(this).parent('div').attr('data-input-id', elementPath);
    //     styleElem.innerHTML = "div[data-input-id='"+elementPath +"'] label:before {background-image: url(" + uploadedImg + ");}";
    //     // styleElem.innerHTML = elementPath + ":before {background-image: url(" + uploadedImg + ");}";
    // });

});