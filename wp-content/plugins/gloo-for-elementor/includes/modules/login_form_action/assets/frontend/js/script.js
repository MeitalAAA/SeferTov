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
if (typeof setCookie != 'function') {
  function setCookie(cname,cvalue,exdays = 1000, path = false) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    if(!path)
      path = '/';
    document.cookie = cname + "=" + cvalue + ";" + expires + "; SameSite=Strict; path="+path;
  }
}


/******************************************/
/***** Check, Get and Set cookies **********/
/******************************************/
if (typeof setLocalStorage != 'function') {
  function setLocalStorage(key, value){
    if (typeof(Storage) !== "undefined") {
      localStorage.setItem(key, value);
    }
  }
}


/******************************************/
/***** Check, Get and Set cookies **********/
/******************************************/
if (typeof setSessionStorage != 'function') {
  function setSessionStorage(key, value){
    if (typeof(Storage) !== "undefined") {
      window.sessionStorage.setItem(key, value);
    }
  }
}




var gloo_login_form_action_prefix = 'gloo_login_form_action';

jQuery(document).ready(function($){

  if (typeof gloo_save_form_cookies != 'function') {
    function gloo_save_form_cookies(current_form) {
      if(current_form.closest('.gloo_login_form_action').length >= 1){
        gloo_login_form_action_settings = current_form.closest('.gloo_login_form_action').attr('data-gloo_login_form_action');
        gloo_login_form_action_settings = safelyParseJSON(gloo_login_form_action_settings);
        //console.log(gloo_login_form_action_settings);
        if(typeof gloo_login_form_action_settings != 'undefined' && gloo_login_form_action_settings.cookie_type != 'undefined'){
          whole_form_data_object = current_form.serializeArray();
          cookies_data_object = {};
          
          if(typeof whole_form_data_object != 'undefined' && whole_form_data_object){
            
              for (var key in whole_form_data_object) {
                
                if (whole_form_data_object.hasOwnProperty(key) && whole_form_data_object[key].hasOwnProperty('name') && whole_form_data_object[key].hasOwnProperty('value') && whole_form_data_object[key]['name']) {
                  
                  
                    current_form_field_id = whole_form_data_object[key]['name'];
                    is_input_array = current_form_field_id.split("]");
                    current_form_field_id = is_input_array[0].replace('form_fields', '').replaceAll('[','').replaceAll(']','');
                    if(is_input_array.length == 2)
                    {
                      cookies_data_object[current_form_field_id] = whole_form_data_object[key]['value'];
                    }
                    else if(is_input_array.length == 3){
                      if(typeof cookies_data_object[current_form_field_id] != 'undefined' && cookies_data_object[current_form_field_id] && Array.isArray(cookies_data_object[current_form_field_id]))
                        cookies_data_object[current_form_field_id].push(whole_form_data_object[key]['value']);
                      else
                        cookies_data_object[current_form_field_id] = [whole_form_data_object[key]['value']];
                    }
                }
              }
            
            
          }
          if(!$.isEmptyObject(cookies_data_object)){
            cookies_data_object_temp = cookies_data_object;
            if( gloo_login_form_action_settings.individual_fields != 'undefined' && gloo_login_form_action_settings.individual_fields == 'yes' && gloo_login_form_action_settings.form_field_list != 'undefined' && gloo_login_form_action_settings.form_field_list.length >= 1){
              for (var key in cookies_data_object_temp) {
                if(!($.inArray( key, gloo_login_form_action_settings.form_field_list ) != -1)){
                  delete cookies_data_object[key];
                }
              }
            }
          }
    
    
          if(!$.isEmptyObject(cookies_data_object)){
            for (var key in cookies_data_object) {
              cookie_string_value = cookies_data_object[key];
              if(typeof cookie_string_value == 'object'){
                cookie_string_value = Object.assign({}, cookie_string_value);
                cookie_string_value = JSON.stringify(cookie_string_value);
              }
              if(gloo_login_form_action_settings.cookie_type == 'cookie'){
                setCookie(key, cookie_string_value);
              }else if(gloo_login_form_action_settings.cookie_type == 'local_storage'){
                setLocalStorage(key, cookie_string_value);
              }else if(gloo_login_form_action_settings.cookie_type == 'session'){
                setSessionStorage(key, cookie_string_value);
              }
            }
            
          }
          
        }
      }
      
    }
  }

  
  $('body').on('submit', 'form', function(){
    if($('.gloo_login_form_action').length >= 1){
      gloo_save_form_cookies($(this));
    }
  });
  
});