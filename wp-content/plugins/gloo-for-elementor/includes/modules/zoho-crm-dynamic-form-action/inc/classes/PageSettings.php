<?php
namespace Gloo\Modules\ZohoCrmDynamicFormAction;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PageSettings extends ZohoCrmDynamicFormAction{
  
  public function __construct(){

    add_action('init', array($this, 'input_handle'));
    add_action( 'admin_menu', array($this,'admin_menu'));

    if(isset($_GET['leads']) && (int) $_GET['leads'] === 1){
      $this->get_module_fields();
    }

    if(isset($_GET['refresh_zoho_data']) && (int) $_GET['refresh_zoho_data'] === 1){
      $this->get_modules_list();
    }
    
  }// construct function end here

  /******************************************/
  /***** page_bboptions_admin_menu function start from here *********/
  /******************************************/
  public function admin_menu(){
    
    /* add sub menu in our wordpress dashboard main menu */
    //add_menu_page(__('Fluid Dynamics', 'gloo'), __('Fluid Dynamics', 'gloo'), 'manage_options', $this->prefix, array($this,'add_submenu_page') );
    add_submenu_page( 
      null,
      __('Zoho CRM Form Submit Action', 'gloo'),
      __('Zoho CRM Form Submit Action', 'gloo'),
      'manage_options', $this->prefix,
      array($this,'add_submenu_page') 
    );
    
  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page(){ ?>
    <div class="wrap bytebunch_admin_page_container">
      <div id="icon-tools" class="icon32"></div>
      <div id="poststuff">
          <div id="postbox-container" class="postbox-container">          
            <form action="" method="post">
            <?php wp_nonce_field($this->prefix('page_update_setting'), $this->prefix('page_update_setting')); ?>
              <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                  <div class="postbox-header">                    
                    <h3 class="hndle ui-sortable-handle"><span><?php _e('Zoho CRM Form Submit Action', 'gloo'); ?></span></h3>
                    <div class="handle-actions hide-if-no-js">
                      <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Author</span><span class="toggle-indicator" aria-hidden="true"></span></button>                    
                    </div>
                  </div><!-- postbox-header-->
                  <div class="inside">
                    <table class="form-table">
                      <tbody>
                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("site_url"); ?>"><?php _e('Site URL', 'gloo'); ?></label></th>
                          <td><input type="text" name="<?php echo $this->prefix("site_url"); ?>" id="<?php echo $this->prefix("site_url"); ?>" value="<?php echo trailingslashit(get_bloginfo('url')); ?>" class="regular-text" style="width:600px; max-width:100%;" disabled="disabled"></td>
                        </tr>
                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("zoho_client_id"); ?>"><?php _e('Client ID', 'gloo'); ?></label></th>
                          <td><input type="text" name="<?php echo $this->prefix("zoho_client_id"); ?>" id="<?php echo $this->prefix("zoho_client_id"); ?>" value="<?php echo $this->get_option('zoho_client_id'); ?>" class="regular-text" style="width:600px; max-width:100%;"></td>
                        </tr>

                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("zoho_client_secret"); ?>"><?php _e('Client Secret', 'gloo'); ?></label></th>
                          <td><input type="text" name="<?php echo $this->prefix("zoho_client_secret"); ?>" id="<?php echo $this->prefix("zoho_client_secret"); ?>" value="<?php echo $this->get_option('zoho_client_secret'); ?>" class="regular-text" style="width:600px; max-width:100%;"></td>
                        </tr>

                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("zoho_data_center"); ?>"><?php _e('Zoho Data Center', 'gloo'); ?></label></th>
                          <td>
                          <?php 
                            $data_centers = array('com' => 'US', 'com.au' => 'AU', 'eu' => 'EU', 'in' => 'IN', 'cn' => 'CN');
                            echo '<select for="'.$this->prefix("zoho_data_center").'" name="'.$this->prefix("zoho_data_center").'" id="'.$this->prefix("zoho_data_center").'">';
                            echo ArraytoSelectList($data_centers, $this->get_option('zoho_data_center'));
                            echo '</select>';
                          ?>
                          </td>
                        </tr>
                        
                        <?php if($this->get_option('zoho_client_id')){ ?>
                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("zoho_authentication_url"); ?>"><?php _e('Zoho Authentication URL', 'gloo'); ?></label></th>
                          <td>
                          <?php
                            //#zoho crm api console for developers
                            //https://accounts.zoho.com/developerconsole
                            //https://www.zoho.com/crm/developer/docs/api/v2/modules-api.html
                            //https://www.zoho.com/crm/developer/docs/api/v2/field-meta.html
                            $zohoURL = 'https://accounts.zoho.'.$this->get_option('zoho_data_center').'/oauth/v2/auth?scope=ZohoCRM.modules.ALL,ZohoCRM.settings.ALL&client_id='.$this->get_option('zoho_client_id').'&prompt=consent&response_type=code&access_type=offline&redirect_uri='.urlencode(trailingslashit(get_bloginfo('url')));
                            echo '<a href="'.$zohoURL.'" target="_blank" class="button button-default button-hero sf_login">Click Here to get access to Zoho CRM</a>'; 
                          ?>
                          </td>
                        </tr>
                        <?php } ?>


                        <?php /*<tr>
                          <th scope="row"><label for="<?php echo $this->prefix("zoho_leads_fields"); ?>"><?php _e('Zoho Leads Fields', 'gloo'); ?></label></th>
                          <td>
                            <?php
                              echo '<a href="'.add_query_arg(array('leads' => 1), $this->setting_page_url()).'">Click Here to get zoho leads fields</a>';
                            ?>
                          </td>
                        </tr> */ ?>


                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("zoho_refresh_token"); ?>"><?php _e('Refresh Token', 'gloo'); ?></label></th>
                          <td><input type="text" name="<?php echo $this->prefix("zoho_refresh_token"); ?>" id="<?php echo $this->prefix("zoho_refresh_token"); ?>" value="<?php echo $this->get_option('zoho_refresh_token'); ?>" class="regular-text" style="width:600px; max-width:100%;" disabled="disabled"></td>
                        </tr>

                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("zoho_access_token"); ?>"><?php _e('Access Token', 'gloo'); ?></label></th>
                          <td><input type="text" name="<?php echo $this->prefix("zoho_access_token"); ?>" id="<?php echo $this->prefix("zoho_access_token"); ?>" value="<?php echo $this->get_option('zoho_access_token'); ?>" class="regular-text" style="width:600px; max-width:100%;" disabled="disabled"></td>
                        </tr>

                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("zoho_form_actions_quantity"); ?>"><?php _e('Number of form Actions', 'gloo'); ?></label></th>
                          <td>
                          <?php
                            $counting_array = array();
                            for($i = 1; $i<= 10; $i++){
                              $counting_array[$i] = $i;
                            }
                            echo '<select for="'.$this->prefix("zoho_form_actions_quantity").'" name="'.$this->prefix("zoho_form_actions_quantity").'" id="'.$this->prefix("zoho_form_actions_quantity").'">';
                            echo ArraytoSelectList($counting_array, $this->get_option('zoho_form_actions_quantity'));
                            echo '</select>';
                          ?>
                          </td>
                        </tr>

                        <?php if($this->get_option('zoho_client_id')){ ?>
                        <tr>
                          <th scope="row"><label for=""><?php _e('Refresh Data', 'gloo'); ?></label></th>
                          <td>
                          <?php
                            echo '<a href="'.$this->setting_page_url(array('refresh_zoho_data' => 1)).'" class="button button-default">'.__('Refresh Zoho Data', 'gloo').'</a>'; 
                          ?>
                          </td>
                        </tr>
                        <?php } ?>

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

      if(isset($_POST[$this->prefix('page_update_setting')]) && wp_verify_nonce($_POST[$this->prefix('page_update_setting')], $this->prefix('page_update_setting')) ){
        
        $formKeys = array(
          'zoho_client_id',
          'zoho_client_secret',
          'zoho_data_center',
          'zoho_form_actions_quantity',
        );
        foreach($formKeys as $formKey){
          if(isset($_POST[$this->prefix($formKey)])){
            $value = sanitize_text_field($_POST[$this->prefix($formKey)]);
            if(!$value)
              $value = '';
            $this->set_option($formKey, $value);
          }
        }

        $this->message = __('Your setting have been updated.', 'gloo');
        add_action( 'admin_notices', [ $this, 'admin_notices' ] );

      }
      

    } // if isset page end here

  } // input handle function end here

  /******************************************/
  /***** setting_page_url function start from here *********/
  /******************************************/
  public function setting_page_url($args = array()){
    $url = get_admin_url(null, 'tools.php?page='.$this->prefix);
    if($args && is_array($args) && count($args) >= 1){
      $url = add_query_arg($args, $url);
    }
    return $url;
  }



}// class end here
