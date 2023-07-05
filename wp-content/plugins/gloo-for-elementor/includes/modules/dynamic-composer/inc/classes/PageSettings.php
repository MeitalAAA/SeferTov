<?php
namespace ByteBunch\FluidDynamics;


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PageSettings extends FluidDynamics{

  
  public function __construct(){

    add_action('init', array($this, 'input_handle'));
    add_action( 'admin_menu', array($this,'admin_menu'));

  }// construct function end here

  /******************************************/
  /***** page_bboptions_admin_menu function start from here *********/
  /******************************************/
  public function admin_menu(){
    
    /* add sub menu in our wordpress dashboard main menu */
    //add_menu_page(__('Fluid Dynamics', 'fluid-dynamics'), __('Fluid Dynamics', 'fluid-dynamics'), 'manage_options', $this->prefix, array($this,'add_submenu_page') );
    add_submenu_page('options-general.php', __('Fluid Dynamics', 'fluid-dynamics'), __('Fluid Dynamics', 'fluid-dynamics'), 'manage_options', $this->prefix, array($this,'add_submenu_page') );
    
  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page(){ ?>
    <div class="wrap bytebunch_admin_page_container">
      <div id="icon-tools" class="icon32"></div>
      <div id="poststuff">
          <div id="postbox-container" class="postbox-container">
          <?php BBWPUpdateErrorMessage(); ?>
            <form action="" method="post">
            <?php wp_nonce_field(); ?>
              <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                  <div class="postbox-header">                    
                    <h3 class="hndle ui-sortable-handle"><span><?php _e('Fluid Dynamics Settings', 'gloo_for_elementor'); ?></span></h3>
                    <div class="handle-actions hide-if-no-js">
                      <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Author</span><span class="toggle-indicator" aria-hidden="true"></span></button>                    
                    </div>
                  </div><!-- postbox-header-->
                  <div class="inside">
                    <input type="hidden" name="<?php echo $this->prefix('page_update_setting'); ?>" value="<?php echo $this->prefix('page_update_setting'); ?>">
                    <table class="form-table">
                      <tbody>
                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("google_api_key"); ?>"><?php _e('Google Maps API key', 'gloo_for_elementor'); ?></label></th>
                          <td><input type="text" name="<?php echo $this->prefix("google_api_key"); ?>" id="<?php echo $this->prefix("google_api_key"); ?>" value="<?php echo $this->get_option('google_api_key'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                          <th scope="row">
                            <label for="<?php echo $this->prefix("input_element_class"); ?>"><?php _e('Input element class name', 'gloo_for_elementor'); ?></label><br>
                            <small>You can assign this class to any input element to make it autocomplete with google api.</small>
                          </th>
                          <td><input type="text" name="<?php echo $this->prefix("input_element_class"); ?>" id="<?php echo $this->prefix("input_element_class"); ?>" value="<?php echo $this->get_option('input_element_class'); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("disable_google_maps_js"); ?>"><?php _e('Disable Google Maps JS', 'gloo_for_elementor'); ?></label></th>
                          <td>
                            <input type="checkbox" name="<?php echo $this->prefix("disable_google_maps_js"); ?>" id="<?php echo $this->prefix("disable_google_maps_js"); ?>" <?php if($this->get_option('disable_google_maps_js')){ echo 'checked="checked"'; } ?>>
                          </td>
                        </tr>
                        <?php /*<tr>
                          <th scope="row">
                            <label for="<?php echo $this->prefix("load_on_pages"); ?>">
                              <?php _e('Load on Pages ', 'fluid-dynamics'); ?><br>
                              <small><?php _e('Page IDs seperated by comma, leave blank if you want to load the javascript of this plugin on all pages.', 'fluid-dynamics'); ?></small>
                            </label>
                          </th>
                          <td>
                            <textarea name="<?php echo $this->prefix("load_on_pages"); ?>" id="<?php echo $this->prefix("load_on_pages"); ?>" cols="30" rows="10" style="width:350px; max-width:100%;"><?php echo $this->get_option('load_on_pages'); ?></textarea>
                          </td>
                        </tr>
                        
                        <tr><th></th><td>
                          <input type="text" id="sdfs" name="sdfs" class="<?php echo $this->get_option('input_element_class'); ?>">
                          <input type="text" id="sdf" name="sdf" class="<?php echo $this->get_option('input_element_class'); ?>">
                        </td></tr> */ ?>
                      </tbody>
                    </table>
                  </div><!-- inside-->
                </div><!-- postbox-->
              </div><!-- meta-box-sortables-->
              <?php submit_button('Save Changes'); ?>
            </form>
          </div><!-- postbox-container-->
      </div><!-- poststuff-->
    </div><!-- wrap-->
    <?php 
  }


  /******************************************/
  /***** input_handle function start from here *********/
  /******************************************/
  public function input_handle(){
    
    if(isset($_GET['page']) && $_GET['page'] === $this->prefix){

      if(isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce']) && isset($_POST[$this->prefix('page_update_setting')])){

        if(isset($_POST[$this->prefix('google_api_key')])){
          $value = sanitize_text_field($_POST[$this->prefix('google_api_key')]);
          $this->set_option('google_api_key', $value);
        }

        if(isset($_POST[$this->prefix('load_on_pages')])){
          $value = sanitize_text_field($_POST[$this->prefix('load_on_pages')]);
          $this->set_option('load_on_pages', $value);
        }

        if(isset($_POST[$this->prefix('input_element_class')])){
          $value = sanitize_text_field($_POST[$this->prefix('input_element_class')]);
          $this->set_option('input_element_class', $value);
        }

        if(isset($_POST[$this->prefix('disable_google_maps_js')]) && $_POST[$this->prefix('disable_google_maps_js')] == 'on'){
          $value = sanitize_text_field($_POST[$this->prefix('disable_google_maps_js')]);
          $this->set_option('disable_google_maps_js', $value);
        }else{
          $this->set_option('disable_google_maps_js', '');
        }

        add_action( 'admin_notices', [ $this, 'adminNotices' ] );

      }
      

    } // if isset page end here

  } // input handle function end here

}// class end here
