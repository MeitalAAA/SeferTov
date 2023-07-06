jQuery(window).on('load', function () {
  if (elementor) {

    jQuery("#elementor-panel-header-menu-button").after('<div class="gloo_go_to_interactor"></div>');
      jQuery('body').on('click', '.gloo_go_to_interactor', function (e) {
          //jQuery(".elementor-tab-control-advanced").trigger('click');
          jQuery("#elementor-panel-footer-settings").trigger('click');
          jQuery("#elementor-panel-page-settings .elementor-tab-control-advanced").trigger('click');
          e.preventDefault();
          return false;
      });

      function gloo_interactor_active() {
        if (jQuery(".elementor-tab-control-advanced").length >= 1) {
            if (jQuery("#elementor-panel-page-settings .elementor-tab-control-advanced").hasClass("elementor-active")) {
                jQuery(".gloo_go_to_interactor").addClass("gloo-active");
            } else {
                jQuery(".gloo_go_to_interactor").removeClass("gloo-active");
            }
        } else if (jQuery(".gloo_go_to_interactor").length >= 1) {
            jQuery(".gloo_go_to_interactor").removeClass("gloo-active");
        }
      }

      jQuery('html').on('click', function () {
          gloo_interactor_active();
      });

      jQuery(document).on('DOMNodeInserted', 'body', function (e) {
          gloo_interactor_active();
      });


    }
});