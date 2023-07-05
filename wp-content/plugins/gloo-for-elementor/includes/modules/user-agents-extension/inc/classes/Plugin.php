<?php

namespace Gloo\Modules\UserAgentsExtension;

// exit if file is called directly
if (!defined('ABSPATH')) {
  exit;
}

class Plugin
{

  private static $instance = null;

  public $message = null;
  public $messageClass = 'success';

  public $prefix = 'gloo_user_agents_extension';
  static $options = array();


  /******************************************/
  /***** Single Ton base intialization of our class **********/
  /******************************************/
  public static function instance()
  {
    if (is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /******************************************/
  /***** class constructor **********/
  /******************************************/
  public function __construct()
  {

    // get the plugin options/settings.
    self::$options = SerializeStringToArray(get_option($this->prefix('options')));

    if (!(isset(self::$options['allowed_devices']) && self::$options['allowed_devices'])) {
      $this->set_option('allowed_devices', '3');
    }

    if (!(isset(self::$options['allowed_mobile_devices']) && self::$options['allowed_mobile_devices'])) {
      $this->set_option('allowed_mobile_devices', '1');
    }

    if (!(isset(self::$options['allowed_desktop_devices']) && self::$options['allowed_desktop_devices'])) {
      $this->set_option('allowed_desktop_devices', '1');
    }

    if (!(isset(self::$options['new_logic']) && self::$options['new_logic'])) {
      $this->set_option('new_logic', 'yes');
    }

    add_action('init', [$this, 'init']);

    if (is_admin()) {

      $PageSettings = new PageSettings();

      // add javascript and css to wp-admin dashboard.
      add_action('admin_enqueue_scripts', array($this, 'wp_admin_style_scripts'));

      //add settings page link to plugin activation page.
      //add_filter( 'plugin_action_links_'.plugin_basename(gloo_user_agents_extension_PLUGIN_FILE), array($this, 'plugin_action_links') );

      // Plugin activation hook
      //register_activation_hook(plugin_basename(gloo_user_agents_extension_PLUGIN_FILE), array($this, 'PluginActivation'));

      // plugin deactivation hook
      //register_deactivation_hook(plugin_basename(gloo_user_agents_extension_PLUGIN_FILE), array($this, 'PluginDeactivation'));

    } else {
      // add javascript and css to front end.
      //add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
    }

    if($this->is_compatible()){
			add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'register_dynamic_tags' ] );
		}

  } // construct function end here


  /******************************************/
  /***** get plugin prefix with custom string **********/
  /******************************************/
  public function prefix($string = '', $underscore = "_")
  {

    return $this->prefix . $underscore . $string;
  } // prefix function end here.


  /******************************************/
  /***** get option function**********/
  /******************************************/
  public function get_option($key)
  {

    if (isset(self::$options[$key]))
      return self::$options[$key];
    else
      return NULL;
  } // get_option


  /******************************************/
  /***** get option function **********/
  /******************************************/
  public function set_option($key, $value)
  {

    self::$options[$key] = $value;
    update_option($this->prefix . '_options', ArrayToSerializeString(self::$options));
  } // set_option


  /******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts()
  {

    //if(is_admin()){
    //wp_enqueue_script( 'postbox' );
    //}

    wp_register_script($this->prefix('script'), gloo()->plugin_url('includes/modules/') . 'user-agents-extension/assets/admin/js/script.js', array('jquery'), '1.0');

    // Localize the script with new data
    $settings_vars = array(
      'new_logic' => $this->get_option('new_logic'),
    );
    wp_localize_script($this->prefix('script'), 'settings_vars', $settings_vars);

    wp_enqueue_script($this->prefix('script'));

    //$js_variables = array('input_element_class' => $this->get_option('input_element_class'));
    //wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );


  } // wp_admin_style_scripts


  /******************************************/
  /***** add javascript and css to front end. **********/
  /******************************************/
  /*public function wp_style_scripts() {

    wp_register_style( $this->prefix('style'), plugin_dir_url(gloo_user_agents_extension_PLUGIN_FILE) . 'assets/css/style.css', array(), '1.0.0' );
    wp_enqueue_style($this->prefix('style'));

    wp_register_script( $this->prefix('script'), plugin_dir_url(gloo_user_agents_extension_PLUGIN_FILE) . 'assets/js/script.js', array(), '1.0');
    wp_enqueue_script( $this->prefix('script') );


    $js_variables = array('input_element_class' => $this->get_option('input_element_class'));
    wp_localize_script(  $this->prefix('script'), $this->prefix, $js_variables );

  }*/



  /******************************************/
  /***** Check if elementor is loaded. **********/
  /******************************************/
  public function is_compatible()
  {

    // Check if Elementor installed and activated
    if (!did_action('elementor/loaded')) {
      $this->message = __('Login Restriction require Elementor Pro to be installed and active.', 'gloo');
      $this->messageClass = 'warning';
      add_action('admin_notices', [$this, 'admin_notices']);
      return false;
    }
    return true;
  }



  /******************************************/
  /***** admin_notice_missing_main_plugin. **********/
  /******************************************/
  public function admin_notices()
  {
    //Value of $class can be error, success, warning and info
    if ($this->message && $this->messageClass) {
      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr('notice notice-' . $this->messageClass . ' is-dismissible'), esc_html($this->message));
    }
  }



  /******************************************/
  /***** Intialize the elementor and other plugins extended classes and functions. **********/
  /******************************************/
  public function init()
  {
    //update_user_meta(2, 'gloo_allowed_devices', ArrayToSerializeString(array()));
    $new_logic = unserialize(get_option('gloo_user_agents_extension_options'));
    if (isset($new_logic['new_logic']) && $new_logic['new_logic'] == 'yes') {
      $PluginSecurity = new PluginSecurity();
    } else {
      $PluginDefault = new PluginDefault();
    }
    //$PluginDefault = new PluginDefault();
    //if ( $this->is_compatible() ) {
    //}
    // Add Plugin actions
    //add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
    //add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );

    // Add pattern attribute to form field render

    //add_action( 'elementor/element/form/section_form_fields/before_section_end', [ $this, 'addAutocompleteAddressFieldControl' ], 100, 2 );

  }

  
	/******************************************/
  /***** register_tags  **********/
  /******************************************/
  public function register_dynamic_tags($dynamic_tags){
    
		/*\Elementor\Plugin::$instance->dynamic_tags->register_group( 'user-agent-request-variables', [
			'title' => 'User Agents Variables' 
		] );*/

		$dynamic_tags->register_tag( '\Gloo\Modules\UserAgentsExtension\SessionRequestDynamicTags' );
		
  }

} // BBWP_CustomFields class
