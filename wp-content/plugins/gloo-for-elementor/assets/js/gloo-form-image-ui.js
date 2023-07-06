jQuery(function ($) {
    // if($("input.gloo-image-upload-ui-input").length >= 1){
    //     $("input.gloo-image-upload-ui-input").each(function(e,i){
    //         existing_image = $(this).closest(".gloo-image-upload-ui-wrapper").attr('data-background-image');
    //         if(existing_image && existing_image != ''){
    //             $(this).prop('required',false);
    //         }
    //     });
    // }

    $(document).on('change', 'input.gloo-image-upload-ui-input', function (event) {
        
        if(event.target.files.length >= 1)
            var uploadedImg = URL.createObjectURL(event.target.files[0]);
        else
            var uploadedImg = '';
        
            
        $(this).closest(".gloo-image-upload-ui-wrapper").attr('data-background-image', uploadedImg);
        var elementPath = fullPath(event.target),
            styleElem = document.head.appendChild(document.createElement("style"));
        $(this).parent('div').attr('data-input-id', elementPath);
        styleElem.innerHTML = "div[data-input-id='"+elementPath +"'] label:before {background-image: url(" + uploadedImg + ")!important;}";
        // styleElem.innerHTML = elementPath + ":before {background-image: url(" + uploadedImg + ");}";
        
        
    });

    function fullPath(el) {
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

});
