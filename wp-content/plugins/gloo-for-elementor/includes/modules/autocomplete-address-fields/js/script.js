/************************************************** */
/***********Document Ready function *************** */
/************************************************** */
function hasClass(element, className) {
    return (' ' + element.className + ' ').indexOf(' ' + className+ ' ') > -1;
}

/************************************************** */
/***********Document Ready function *************** */
/************************************************** */
(function(funcName, baseObj) {
  "use strict";
  // The public function name defaults to window.docReady
  // but you can modify the last line of this function to pass in a different object or method name
  // if you want to put them in a different namespace and those will be used instead of 
  // window.docReady(...)
  funcName = funcName || "docReady";
  baseObj = baseObj || window;
  var readyList = [];
  var readyFired = false;
  var readyEventHandlersInstalled = false;
  
  // call this when the document is ready
  // this function protects itself against being called more than once
  function ready() {
      if (!readyFired) {
          // this must be set to true before we start calling callbacks
          readyFired = true;
          for (var i = 0; i < readyList.length; i++) {
              // if a callback here happens to add new ready handlers,
              // the docReady() function will see that it already fired
              // and will schedule the callback to run right after
              // this event loop finishes so all handlers will still execute
              // in order and no new ones will be added to the readyList
              // while we are processing the list
              readyList[i].fn.call(window, readyList[i].ctx);
          }
          // allow any closures held by these functions to free
          readyList = [];
      }
  }
  
  function readyStateChange() {
      if ( document.readyState === "complete" ) {
          ready();
      }
  }
  
  // This is the one public interface
  // docReady(fn, context);
  // the context argument is optional - if present, it will be passed
  // as an argument to the callback
  baseObj[funcName] = function(callback, context) {
      if (typeof callback !== "function") {
          throw new TypeError("callback for docReady(fn) must be a function");
      }
      // if ready has already fired, then just schedule the callback
      // to fire asynchronously, but right away
      if (readyFired) {
          setTimeout(function() {callback(context);}, 1);
          return;
      } else {
          // add the function and context to the list
          readyList.push({fn: callback, ctx: context});
      }
      // if document already ready to go, schedule the ready function to run
      // IE only safe when readyState is "complete", others safe when readyState is "interactive"
      if (document.readyState === "complete" || (!document.attachEvent && document.readyState === "interactive")) {
          setTimeout(ready, 1);
      } else if (!readyEventHandlersInstalled) {
          // otherwise if we don't have event handlers installed, install them
          if (document.addEventListener) {
              // first choice is DOMContentLoaded event
              document.addEventListener("DOMContentLoaded", ready, false);
              // backup is window load event
              window.addEventListener("load", ready, false);
          } else {
              // must be IE
              document.attachEvent("onreadystatechange", readyStateChange);
              window.attachEvent("onload", ready);
          }
          readyEventHandlersInstalled = true;
      }
  }
})("docReady", window);

/************************************************** */
/***********Observe Changes in Dom *************** */
/************************************************** */
var observeDOM = (function(){
    var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
  
    return function( obj, callback ){
        
      if( !obj || obj.nodeType !== 9 ) return;
     
      if( MutationObserver ){
        // define a new observer
        var mutationObserver = new MutationObserver(callback)
  
        // have the observer observe foo for changes in children
        mutationObserver.observe( obj, { childList:true, subtree:true })
        return mutationObserver
      }
      
      // browser support fallback
      else if( window.addEventListener ){
        obj.addEventListener('DOMNodeInserted', callback, false)
        obj.addEventListener('DOMNodeRemoved', callback, false)
      }
    }
  })();




  if (typeof add_auto_complete_to_single_field != 'function') {
    function add_auto_complete_to_single_field(input_field){
        autocomplete = new google.maps.places.Autocomplete(input_field, {types: ["geocode"]});
        if(typeof gloo_autocomplete_address.supported_countries != 'undefined'){
            autocomplete.setComponentRestrictions({
                country: gloo_autocomplete_address.supported_countries,
            });
        }
    }
  }




  if (typeof load_google_maps_auto_complete != 'function') {
    function load_google_maps_auto_complete(){
        //Google api for address fields
        otw_autocomplete_list = document.getElementsByClassName(gloo_autocomplete_address.input_element_class);
        //console.log(otw_autocomplete_list);
        if(otw_autocomplete_list && otw_autocomplete_list.length >= 1){
            for (i = 0; i < otw_autocomplete_list.length; i++) {
                if(!hasClass(otw_autocomplete_list[i], "has_"+gloo_autocomplete_address.input_element_class)){
                    otw_autocomplete_list[i].classList.add("has_"+gloo_autocomplete_address.input_element_class);
                    
                    if(otw_autocomplete_list[i].type == 'text'){

                        add_auto_complete_to_single_field(otw_autocomplete_list[i]);
                                    
                    }else{
                        otw_autocomplete_list_inner = otw_autocomplete_list[i].querySelectorAll('input[type="text"]');
                        if(otw_autocomplete_list_inner && otw_autocomplete_list_inner.length >= 1){
                            for (j = 0; j < otw_autocomplete_list_inner.length; j++) {
                                add_auto_complete_to_single_field(otw_autocomplete_list_inner[j]);
                                // autocomplete = new google.maps.places.Autocomplete(otw_autocomplete_list_inner[j], { types: ["cities"] });
                                // if(typeof gloo_autocomplete_address.supported_countries != 'undefined'){
                                //     autocomplete.setComponentRestrictions({
                                //         country: gloo_autocomplete_address.supported_countries,
                                //     });
                                // }
                            }
                        }
                    }
                }
                
            }
        }
    }
  }


docReady(function() {

    /*jQuery(document).on('DOMNodeInserted', 'body', function (e) {
        load_google_maps_auto_complete();
    });*/
    // Observe a specific DOM element:
    observeDOM( document, function(m){ 
        load_google_maps_auto_complete();
    });
    load_google_maps_auto_complete();
    

  postbox_container = document.getElementById('postbox-container');

  if(postbox_container){
    postboxes.save_state = function(){
      return;
    };
    postboxes.save_order = function(){
        return;
    };
    postboxes.add_postbox_toggles();
  }

  select_two_input = document.getElementsByClassName('gloo_select_two_input');
  
  if(select_two_input && select_two_input.length >= 1){
    jQuery(".gloo_select_two_input").select2();
  }
    

});

