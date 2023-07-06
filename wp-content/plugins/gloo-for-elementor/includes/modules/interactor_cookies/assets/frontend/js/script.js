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


/******************************************/
/***** Check, Get and Set cookies **********/
/******************************************/
if (typeof set_interactor_cookies != 'function') {
  function set_interactor_cookies(key, value, cookie_type = 'cookie'){
    if(typeof value == 'object')
      cookie_string_value = JSON.stringify(value);
    if(cookie_type == 'cookie'){
      setCookie(key, cookie_string_value);
    }else if(cookie_type == 'local_storage'){
      setLocalStorage(key, cookie_string_value);
    }else if(cookie_type == 'session'){
      setSessionStorage(key, cookie_string_value);
    }
  }
}