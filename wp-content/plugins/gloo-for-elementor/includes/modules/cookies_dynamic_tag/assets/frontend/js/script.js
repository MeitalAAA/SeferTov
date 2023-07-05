if (typeof safelyParseJSON != 'function') {
  function safelyParseJSON (json) {
    // This function cannot be optimised, it's best to
    // keep it small!
    var parsed;

    try {
      parsed = JSON.parse(json)
    } catch (e) {
      // Oh well, but whatever...
    }

    return parsed // Could be undefined!
  }
}

/******************************************/
/***** Check, Get and Set cookies **********/
/******************************************/


if (typeof getCookie != 'function') {
  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
}



/******************************************/
/***** Check, Get and Set cookies **********/
/******************************************/

if (typeof getLocalStorage != 'function') {
  function getLocalStorage(key){
    if (typeof(Storage) !== "undefined") {
      return localStorage.getItem(key);
    }
  }
}


/******************************************/
/***** Check, Get and Set cookies **********/
/******************************************/
if (typeof getSessionStorage != 'function') {
  function getSessionStorage(key){
    if (typeof(Storage) !== "undefined") {
      return window.sessionStorage.getItem(key);
    }
  }
}


if (typeof gloo_array_to_html != 'function') {
  function gloo_array_to_html(array, data_settings){
    original_object = array;
    array = Object.values(array);
    output = '';
    if(typeof data_settings.field_output != 'undefined'){
      if(data_settings.field_output == 'type_lenght'){
        output += array.length;
      }
      else if(data_settings.field_output == 'type_specific_array_index'){
        if(typeof data_settings.data_index != 'undefined' && data_settings.data_index == 'first_index'){
          output += array[0];
        }else if(typeof data_settings.data_index != 'undefined' && data_settings.data_index == 'last_index'){
          // output += array[array.length-1];'
          output += array.pop();
        }
        else if(typeof data_settings.data_index != 'undefined' && data_settings.data_index == 'specific_index' && typeof data_settings.array_index != 'undefined' && original_object.hasOwnProperty(data_settings.array_index)){
          // output += array[parseInt(data_settings.array_index)-1];
          output += original_object[data_settings.array_index];
        }
      }
      else if(data_settings.field_output == 'type_delimeter' && typeof data_settings.delimiter != 'undefined' && data_settings.delimiter){
        output += array.join(data_settings.delimiter);
      }
      else if(data_settings.field_output == 'type_ul' || data_settings.field_output == 'type_ol'){
        start_html_wrapper = '<ol>';
        end_html_wrapper = '</ol>';
        start_html_element = '<li>';
        end_html_element = '</li>';
        if(data_settings.field_output == 'type_ul'){
          start_html_wrapper = '<ul>';
          end_html_wrapper = '</ul>';
        }
        output += start_html_wrapper;
        for (var i = 0; i < array.length; i++) { 
          output += start_html_element+array[i]+end_html_element;
        }
        output += end_html_wrapper;
      }
    }
    return output;
  }
}

if (typeof gloo_get_stored_cookie_value != 'function') {
  function gloo_get_stored_cookie_value(data_settings){
    form_data = '';
    output = '';
    output_array = {};
    if(data_settings.cookie_id != 'undefined' && data_settings.cookie_type != 'undefined'){
      if(data_settings.cookie_type == 'cookie'){
        form_data = getCookie(data_settings.cookie_id);
      }else if(data_settings.cookie_type == 'local_storage'){
        form_data = getLocalStorage(data_settings.cookie_id);
      }else if(data_settings.cookie_type == 'session'){
        form_data = getSessionStorage(data_settings.cookie_id);
      }
      
      
      if(typeof form_data != 'undefined' && form_data){
        
        if(data_settings.is_array == 'yes'){
          form_data = safelyParseJSON(form_data);
          output_array = form_data;
        }else{
          output = form_data;
        }
        // for (var key in form_data) {
        //   if (form_data.hasOwnProperty(key) && form_data[key].hasOwnProperty('name') && form_data[key].hasOwnProperty('value')) {
        //     if(data_settings.is_array == 'yes' &&  form_data[key]['name'] == 'form_fields['+data_settings.cookie_id+'][]'){
        //       output_array.push(form_data[key]['value']);
        //     }else if(data_settings.is_array != 'yes' && form_data[key]['name'] == 'form_fields['+data_settings.cookie_id+']'){
        //       output += form_data[key]['value'];
        //     }
        //   }
        // }
      }
    }

    
    if(!jQuery.isEmptyObject(output_array)){
      // console.log(output_array);
      // console.log(output_array.length);
      output = gloo_array_to_html(output_array, data_settings);
    }
    return output;
  }
}

// var gloo_cookie_dynamic_tag_prefix = 'gloo_cookie_dynamic_tag';

jQuery(document).ready(function($){

  $("div.gloo_cookie_dynamic_tag").each(function(){
    data_settings = $(this).attr('data-settings');
    data_settings = safelyParseJSON(data_settings);
    if(typeof data_settings != 'undefined' && data_settings.is_php_cookie != 'undefined' && data_settings.is_php_cookie != 'yes'){
      let output_value = gloo_get_stored_cookie_value(data_settings);
      if(output_value){
        $(this).html(output_value);
        // $(this).parent().html(output);
      }
    }
  });


  if(typeof gloo_input_cookies_object == 'object'){
    for (const key in gloo_input_cookies_object) {
      if (gloo_input_cookies_object.hasOwnProperty(key)) {
        if($("input[value='"+key+"']").length >= 1){

          $("input[value='"+key+"']").each(function(){
            // data_settings = safelyParseJSON(gloo_input_cookies_object[key]);
            data_settings = gloo_input_cookies_object[key];
            // console.log(data_settings);
            if(typeof data_settings != 'undefined' && data_settings.is_php_cookie != 'undefined' && data_settings.is_php_cookie != 'yes'){
              let output_value = gloo_get_stored_cookie_value(data_settings);
              if(output_value){
                $(this).val(output_value);
              }
              else if(typeof data_settings.Fallback != 'undefined' && data_settings.Fallback){
                $(this).val(data_settings.Fallback);
              }else{
                $(this).val('');
              }
            }
          });

        }
      }
    }
  }


});