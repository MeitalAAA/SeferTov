<?php
namespace Gloo\Modules\Dynamic_Nav;


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PageSettings{

  public $message = null;
  public $messageClass = 'success';

  public $pageFields = array();
  public $prefix = 'gloo_dnm_elementor_addon';

  public function __construct(){

    $this->pageFields = array(
      //'host' => array('type' => 'select', 'label' => __('Web Host', 'dnm-elementor-addon')),
      'repeater_item_label' => array('type' => 'text', 'label' => __('Label', 'dnm-elementor-addon')),         
      'repeater_item_meta_key' => array('type' => 'text', 'label' => __('Meta Key', 'dnm-elementor-addon')),
    );
    //$this->pageFieldsSkipSaving = array('cron_time');

    add_action('init', array($this, 'input_handle'));
    add_action( 'admin_menu', array($this,'admin_menu'));

  }// construct function end here

  /******************************************/
  /***** page_bboptions_admin_menu function start from here *********/
  /******************************************/
  public function admin_menu(){
    
    /* Woo Gloo Settings Page */
    add_submenu_page(
      null, // hide from menu
      __('Gloo Dynamic Nav', 'gloo'),
      __('Gloo Dynamic Nav', 'gloo'),
      'manage_options',
      $this->prefix,
      [$this, 'add_submenu_page']
    );
	
  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page(){
    //$gloo_license_info = get_option('gloo_license_info');
    
    
    //$gloo_data = get_plugin_data(dirname(dirname(dirname(dirname(plugin_dir_path(__FILE__))))).'/gloo-for-elementor.php');
    
    include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' );
    $images_url = gloo()->plugin_url( 'includes/modules/dynamic-nav/assets/images/admin/'); 
    ?>
      
        <div class="gloo-item-container">
          <img src="<?php echo $images_url . 'GlooHero.png' ?>" class="gloo-building"/>
          <div class="gloo-items">
            <form action="" method="post">
              <?php 
              wp_nonce_field();
              //wp_nonce_field($this->prefix('page_update_setting'), $this->prefix('page_update_setting')); 
              ?>

              <input type="hidden" name="<?php echo $this->prefix('page_update_setting'); ?>" value="<?php echo $this->prefix('page_update_setting'); ?>">
              <div id="poststuff">
                <div id="postbox-container" class="postbox-container">
                  
                    <div class="meta-box-sortables ui-sortable">
                      <div class="postbox">
                        <div class="postbox-header">                    
                          <h3 class="hndle ui-sortable-handle"><span><?php _e('Gloo Dynamic Nav Menus', 'gloo_for_zoho_td'); ?></span></h3>
                          <div class="handle-actions hide-if-no-js">
                            <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Author</span><span class="toggle-indicator" aria-hidden="true"></span></button>                    
                          </div>
                        </div><!-- postbox-header-->
                        <div class="inside">

                          <div class="add_new_li_button_parent">
                            <ul class="repeatable_fields">
                            <?php 
                            
                            $existing_items = SerializeStringToArray(get_option($this->prefix.'_repeater_items'));
                            
                            if(!($existing_items && is_array($existing_items) && count($existing_items) >= 1))
                              $existing_items = array();
                            ?>
                              <?php if(isset($existing_items) && is_array($existing_items) && count($existing_items) >= 1){
                                foreach($existing_items as $key=>$item){ ?>
                                  <li>
                                    <div class="removebutton_container"><a href="#" class="removeButton"><img src="<?php echo $images_url;  ?>error_message.png" alt=""></a></div>
                                    <h3><?php echo $item['label'] ?></h3>
                                    <div class="clearboth"></div>
                                    <div class="repeater_item_container">
                                      <table class="form-table">
                                        <tbody>                                  
                                          <?php 
                                          echo $this->displayRepeaterTrField('repeater_item_label', $item['label']);
                                          echo $this->displayRepeaterTrField('repeater_item_meta_key', $item['meta_key']);
                                          $this->displayRepeaterTrSelectField('repeater_item_nav_menu', $item['menu']);
                                          ?>
                                        </tbody>
                                      </table>
                                    </div><!-- repeater_item_container-->
                                  </li>
                                <?php }
                              }else{ ?>
                              <li>
                                <div class="removebutton_container" style="display:none;"><a href="#" class="removeButton"><img src="<?php echo $images_url;  ?>error_message.png" alt=""></a></div>
                                <h3>Item</h3>
                                <div class="clearboth"></div>
                                <div class="repeater_item_container">
                                  <table class="form-table">
                                    <tbody>                                  
                                      <?php 
                                      echo $this->displayRepeaterTrField('repeater_item_label');
                                      echo $this->displayRepeaterTrField('repeater_item_meta_key');
                                      $this->displayRepeaterTrSelectField('repeater_item_nav_menu');
                                      ?>
                                    </tbody>
                                  </table>
                                </div><!-- repeater_item_container-->
                                
                              </li>
                              <?php } ?>
                            </ul>

                            <br>
                            <a href="#" class="addButton add_new_li_button button button-default button-hero" data-target="add_new_repeater_item_html"><span><img src="<?php echo $images_url;  ?>plus.png" alt=""></span>Add Item</a>
                            
                            <div class="hidden add_new_repeater_item_html">
                              <div class="removebutton_container">
                                <a href="#" class="removeButton"><img src="<?php echo $images_url;  ?>error_message.png" alt=""></a>
                              </div>
                              <h3>Item</h3>
                              <div class="clearboth"></div>
                              <div class="repeater_item_container">
                                <table class="form-table">
                                  <tbody>                                  
                                    <?php 
                                    echo $this->displayRepeaterTrField('repeater_item_label');
                                    echo $this->displayRepeaterTrField('repeater_item_meta_key');
                                    $this->displayRepeaterTrSelectField('repeater_item_nav_menu');
                                    ?>
                                  </tbody>
                                </table>
                              </div><!-- repeater_item_container-->
                            </div><!-- add_new_repeater_item_html -->
                          </div><!-- add_new_li_button_parent-->

                          
                        </div><!--inside -->
                      </div><!--postbox -->
                    </div><!-- meta-box-sortables -->
                  </div><!-- postbox-container -->
                </div><!-- poststuff -->            
          
                <?php submit_button('Save Changes'); ?>
            </form>
          </div><!-- gloo-items End-->
        </div><!-- gloo-item-container End-->

        <?php
        include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' );
	//$this->bbwp_flipswitch_css();
  }


  /******************************************/
  /***** input_handle function start from here *********/
  /******************************************/
  public function input_handle(){
    
    if(isset($_GET['page']) && $_GET['page'] === $this->prefix){

      if(isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce']) && isset($_POST[$this->prefix('page_update_setting')])){
        $existing_values = array();
        if(isset($_POST[$this->prefix('repeater_item_label')]) && is_array($_POST[$this->prefix('repeater_item_label')]) && count($_POST[$this->prefix('repeater_item_label')]) >= 1){
          foreach($_POST[$this->prefix('repeater_item_label')] as $key=>$value){
            if(isset($_POST[$this->prefix('repeater_item_label')][$key]) && $_POST[$this->prefix('repeater_item_label')][$key] && isset($_POST[$this->prefix('repeater_item_meta_key')][$key]) && $_POST[$this->prefix('repeater_item_meta_key')][$key]){
              $label_value = sanitize_text_field($_POST[$this->prefix('repeater_item_label')][$key]);
              $meta_key_value = sanitize_text_field($_POST[$this->prefix('repeater_item_meta_key')][$key]);
              $nav_menu_value = sanitize_text_field($_POST[$this->prefix('repeater_item_nav_menu')][$key]);
              $existing_values[$meta_key_value] = array('label' => $label_value, 'menu' => $nav_menu_value, 'meta_key' => $meta_key_value);
            }
          }
        }
        update_option($this->prefix('repeater_items'), serialize($existing_values));
    

		    //$this->save_empty('enable_sms');
        add_action( 'admin_notices', [ $this, 'admin_notices' ] );

      }
      

    } // if isset page end here

  } // input handle function end here


  public function pageURL($param = array()){
    return add_query_arg($param, get_admin_url(null, 'admin.php?page='.$this->prefix));
  }


  /******************************************/
  /***** displayField function start from here *********/
  /******************************************/
  public function displayRepeaterTextField($field_key, $default_value = '', $label = '', $placeholder = ''){
    $output = '';
    if($placeholder)
      $placeholder = ' placeholder="'.$placeholder.'"';
    $output = '<div class="form-element">
    <label for="'.$field_key.'" class="field_label">'.$label.'</label>
    <input type="text" name="'.$field_key.'[]" id="'.$field_key.'" value="'.$default_value.'" '.$placeholder.'>
    </div>';
    return $output;
  }

    /******************************************/
  /***** displayField function start from here *********/
  /******************************************/
  public function displayRepeaterTrField($field_key, $value = ''){
    $output = '';
    if(isset($this->pageFields[$field_key])){
      $placeholder = '';
      if(isset($this->pageFields[$field_key]['placeholder']))
        $placeholder = ' placeholder="'.$this->pageFields[$field_key]['placeholder'].'"';
      $output = '<tr>
      <th scope="row"><label for="'. $this->prefix($field_key).'">'. $this->pageFields[$field_key]['label'].'</label></th>
      <td><input type="text" name="'.$this->prefix($field_key).'[]" id="'.$this->prefix($field_key).'" value="'.$value.'" class="regular-text" style="width:400px; max-width:100%;"'.$placeholder.'></td>
      </tr>';
    }
    return $output;
  }

  /******************************************/
  /***** displayField function start from here *********/
  /******************************************/
  public function displayRepeaterTrSelectField($field_key, $value = ''){
    echo '<tr>
    <th scope="row"><label for="">Default Nav Menu</label></th>
    <td>';
    $menus   = wp_get_nav_menus();
		$options = '';
		foreach ( $menus as $key => $menu ) {
			$is_selected = isset( $value ) && $value == $menu->slug ? 'selected' : '';
			$options     .= "<option value='$menu->slug' $is_selected>$menu->name</option>";
		}
		if ( $options ) {
			echo "<select name='".$this->prefix($field_key)."[]' id='term-meta-text'><option value=''>" . __( '--Select--', 'dnm-elementor-addon' ) . "</option>$options</select>";
		}
    
    echo '</td>
  </tr>';
    
  }

  /******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.

  
  /******************************************/
  /***** admin_notice_missing_main_plugin. **********/
  /******************************************/
  public function admin_notices() {
    //Value of $class can be error, success, warning and info
    if($this->message && $this->messageClass){ 
      printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-'.$this->messageClass.' is-dismissible' ), esc_html( $this->message ) );
    }
  }

}// class end here
