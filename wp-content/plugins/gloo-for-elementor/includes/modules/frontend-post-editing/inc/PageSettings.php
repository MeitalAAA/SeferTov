<?php
namespace Gloo\Modules\Form_Post_Editing;


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PageSettings{

  public $prefix = 'gloo_frontend_post_editing';

  public function __construct(){

    add_action('init', array($this, 'input_handle'));
    add_action( 'admin_menu', array($this,'admin_menu'));

  }// construct function end here

  /******************************************/
  /***** page_bboptions_admin_menu function start from here *********/
  /******************************************/
  public function admin_menu(){
    
    /* add sub menu in our wordpress dashboard main menu */
    add_submenu_page(
      null, // hide from menu
      __('Post Editing', 'gloo'), 
      __('Post Editing', 'gloo'), 
      'manage_options', 
      $this->prefix, 
      array($this,'add_submenu_page') 
    );
    
  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page(){

    include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); ?>
    <div class="bytebunch_admin_page_container">
      <div id="icon-tools" class="icon32"></div>
      <div id="poststuff">
          <div id="postbox-container" class="postbox-container">
          
            <form action="" method="post">
            <?php wp_nonce_field(); ?>
              <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                  <div class="postbox-header">                    
                    <h3 class="hndle ui-sortable-handle"><span><?php _e('Post Editing Form Actions', 'gloo_for_elementor'); ?></span></h3>
                    <div class="handle-actions hide-if-no-js">
                      <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Author</span><span class="toggle-indicator" aria-hidden="true"></span></button>                    
                    </div>
                  </div><!-- postbox-header-->
                  <div class="inside">
                    <input type="hidden" name="<?php echo $this->prefix('page_update_setting'); ?>" value="<?php echo $this->prefix('page_update_setting'); ?>">
                    <table class="form-table">
                      <tbody>
                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("form_actions_quantity"); ?>"><?php _e('Form Actions Quantity', 'gloo_for_elementor'); ?></label></th>
                          <td><input type="number" min="1" max="100" name="<?php echo $this->prefix("form_actions_quantity"); ?>" id="<?php echo $this->prefix("form_actions_quantity"); ?>" value="<?php echo get_option($this->prefix("form_actions_quantity"), 1); ?>" class="regular-text"></td>
                        </tr>                  
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
    include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' );
  }


  /******************************************/
  /***** input_handle function start from here *********/
  /******************************************/
  public function input_handle(){
    
    if(isset($_GET['page']) && $_GET['page'] === $this->prefix){

      if(isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce']) && isset($_POST[$this->prefix('page_update_setting')])){

        if(isset($_POST[$this->prefix('form_actions_quantity')]) && $_POST[$this->prefix('form_actions_quantity')]){
          $value = sanitize_text_field($_POST[$this->prefix('form_actions_quantity')]);
          update_option($this->prefix('form_actions_quantity'), $value);
        }else{
          update_option($this->prefix('form_actions_quantity'), 1);
        }

        // add_action( 'admin_notices', [ $this, 'adminNotices' ] );

      }
      

    } // if isset page end here

  } // input handle function end here


  /******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.
}// class end here
