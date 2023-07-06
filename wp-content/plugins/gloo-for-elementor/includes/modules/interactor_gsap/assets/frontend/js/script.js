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

jQuery(document).ready(function($){
  gsap.registerPlugin(ScrollTrigger);
});