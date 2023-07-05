if (typeof isIE != 'function') {
  function isIE(){
    var ua = window.navigator.userAgent;
  
    return ua.indexOf('MSIE ') > 0 || ua.indexOf('Trident/') > 0 || ua.indexOf('Edge/') > 0
  }
}

jQuery(document).ready(function($){
  // console.log(gloo_otp_action);
 
  $("form[data-gloo-otp]").each(function(){
    gloo_otp_action = $(this).attr('data-gloo-otp');
    gloo_otp_action = JSON.parse(gloo_otp_action);
    submit_button = $(this).find('button[type="submit"]');
    
    if(submit_button.length >= 1){
      old_label = submit_button.text();
      $(this).attr('data-label', old_label.trim());
      
      if(typeof gloo_otp_action != 'undefined' && gloo_otp_action.otp_type != 'undefined'){
        
        if(gloo_otp_action.otp_button_label != 'undefined'){
          $(this).attr('data-form-id', gloo_otp_action.form_id);
        }

        if(gloo_otp_action.otp_button_label != 'undefined'){
          submit_button.text(gloo_otp_action.otp_button_label);
          $(this).attr('data-label-otp', gloo_otp_action.otp_button_label);
        }
        // if(gloo_otp_action.otp_type == 'email'){
        //   if(typeof gloo_otp_action.otp_frontend_heading != 'undefined' && gloo_otp_action.otp_frontend_heading)
        //     submit_button.text(gloo_otp_action.otp_frontend_heading);
        // }else if(gloo_otp_action.otp_type == 'sms'){
        //   if(typeof gloo_otp_action.otp_frontend_heading_sms != 'undefined' && gloo_otp_action.otp_frontend_heading_sms)
        //     submit_button.text(gloo_otp_action.otp_frontend_heading_sms);
        // }
      }
    }
    
  });


  
	$(document).ajaxComplete(function(event, xhr, options){
    
    if(typeof xhr != 'undefined' && typeof options != 'undefined' && typeof options.data != 'undefined' && typeof options.data.has != 'undefined' && options.data.has('form_id') && options.data.has('action') && options.data.get('action') == 'elementor_pro_forms_send_form'){
      if(typeof xhr.responseText != 'undefined' && xhr.responseText){
        var response = JSON.parse(xhr.responseText);
        var form_id = options.data.get('form_id');
        parent_form = $("form[data-form-id='"+form_id+"']");
        if(typeof response != 'undefined' && response && typeof response.success != 'undefined' && parent_form.length >= 1 && typeof response.data != 'undefined' && typeof response.data.otp_length != 'undefined' /*&& typeof response.data.frontend_heading != 'undefined'*/ && typeof response.data.form_id != 'undefined' && response.data.form_id == form_id){
          
          // parent_form = parent_form.closest('form');
          var otp_length = Number(response.data.otp_length);
          if (!response.success && otp_length) {
            parent_form.find('.elementor-message').removeClass('elementor-message-danger').addClass('elementor-message-success');
            parent_form.find('.elementor-message').remove();
            parent_form.find('.gloo-otp-container').remove();
            container_html = '<div class="gloo-otp-container elementor-field-group">';
            container_html += '<div class="gloo-otp-box">';
            container_html += '<div class="gloo-otp-channel">';
            //<p>'+response.data.frontend_heading+'</p>
            container_html += '<div class="gloo-otp-heading"></div>';
            container_html += '<div class="gloo-otp-inputs">';
            for(i=0; i < otp_length; i++){
              container_html += '<input class="gloo-otp-input" name="form_fields[gloo_otp]['+i+']" type="tel" maxlength="1" data-index="'+i+'" required="required">';
            }
            container_html += '</div><!-- gloo-otp-input-->';
            container_html += '</div><!-- gloo-otp-channel-->'; 
            container_html += '</div><!-- gloo-otp-box-->'; 
            container_html += '</div><!-- gloo-otp-container-->'; 
            parent_form.find('button[type="submit"]').closest('.elementor-field-group').before(container_html);
            parent_form.find('button[type="submit"]').text(parent_form.attr('data-label'));
            
            let display_message = parent_form.closest('.elementor-element').find('.gloo_otp_heading_container').html();
            // if(typeof response.data.renew_message != 'undefined' && response.data.renew_message)
            //   display_message = response.data.renew_message;
            parent_form.find(".gloo-otp-heading").html(display_message);
          }
        }else if(typeof response != 'undefined' && response && typeof response.success != 'undefined' && response.success == true && parent_form.length >= 1){
          parent_form.find('.gloo-otp-container').remove();
          parent_form.find('button[type="submit"]').text(parent_form.attr('data-label-otp'));
        }
      }
    }

  });

  $(document).on('keyup', '.gloo-otp-container input', function(e){
    var allowed_keys = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105];
    if($(this).val() && allowed_keys.includes(e.keyCode)){
      $(this).next().focus();
    }
  });
  $(document).on('paste', '.gloo-otp-container input', function(e){
    var content = '';
    if (isIE()) {
      //IE allows to get the clipboard data of the window object.
      content = window.clipboardData.getData('text');
    } else {
      //This works for Chrome and Firefox.
      content = e.originalEvent.clipboardData.getData('text/plain');
    }

    if(typeof content == 'string' && content && content != '' && content.length >= 2){
      current_object = $(this);
      for (var i = 0; i < content.length; i++) {
        if(current_object.hasClass('gloo-otp-input')){
          current_object.val(content.charAt(i));
          current_object = current_object.next();
        }
      }
    }
    
  });
});