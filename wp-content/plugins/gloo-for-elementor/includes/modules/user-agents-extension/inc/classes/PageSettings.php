<?php

namespace Gloo\Modules\UserAgentsExtension;


// exit if file is called directly
if (!defined('ABSPATH')) {
  exit;
}

class PageSettings extends Plugin
{

  public $pageFields = array();
  public $pageFieldsSkipSaving = array();

  public function __construct()
  {

    $this->pageFields = array(
      'allowed_devices' => array(),
      'new_logic' => array(),
      'limit_admin' => array(),
      'email_message_new' => array(),
      'allowed_mobile_devices' => array(),
      'allowed_desktop_devices' => array(),
      'on_failure_send_email_to' => array('type' => 'text', 'label' => __('On failure send email to', 'gloo')),
      'login_page_url' => array('type' => 'text', 'label' => __('Login Page URL', 'gloo')),
      'from_name' => array('type' => 'text', 'label' => __('From Name', 'gloo')),
      'from_email' => array('type' => 'text', 'label' => __('From Email', 'gloo')),
      'email_subject' => array('type' => 'text', 'label' => __('Email Subject', 'gloo')),
      'disable_wp_login' => array(),
    );
    //$this->pageFieldsSkipSaving = array('cron_time');

    add_action('init', array($this, 'input_handle'));
    add_action('admin_menu', array($this, 'admin_menu'));
  } // construct function end here

  /******************************************/
  /***** page_bboptions_admin_menu function start from here *********/
  /******************************************/
  public function admin_menu()
  {

    /* add sub menu in our wordpress dashboard main menu */
    //add_menu_page(__('User Agents Extension', 'gloo'), __('User Agents Extension', 'gloo'), 'manage_options', $this->prefix, array($this,'add_submenu_page') );
    add_submenu_page(
      //gloo()->admin_page,
      null,// hide from menu
      __('User Agents Extension', 'gloo'),
      __('User Agents Extension', 'gloo'),
      'manage_options',
      $this->prefix,
      array($this, 'add_submenu_page')
    );
  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page()
  { ?>
    <div class="wrap bytebunch_admin_page_container">
      <div id="icon-tools" class="icon32"></div>
      <div id="poststuff">
        <div id="postbox-container" class="postbox-container">
          <form action="" method="post">
            <?php wp_nonce_field(); ?>
            <div class="meta-box-sortables ui-sortable">
              <div class="postbox">
                <div class="postbox-header">
                  <h3 class="hndle ui-sortable-handle"><span><?php _e('User Agents Extension Settings', 'gloo'); ?></span></h3>
                  <div class="handle-actions hide-if-no-js">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Author</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                  </div>
                </div><!-- postbox-header-->
                <div class="inside">
                  <input type="hidden" name="<?php echo $this->prefix('page_update_setting'); ?>" value="<?php echo $this->prefix('page_update_setting'); ?>">
                  <table class="form-table">
                    <tbody>
                      <tr>
                        <th scope="row"><label for="<?php echo $this->prefix("new_logic"); ?>"><?php _e('Limit Login', 'gloo'); ?></label></th>
                        <td>
                          <?php $new_logic = $this->get_option('new_logic'); ?>
                          <select name="<?php echo $this->prefix("new_logic"); ?>" id="<?php echo $this->prefix("new_logic"); ?>" class="">
                            <option value="no" <?= ($new_logic == 'no' ? ' selected="selected"' : '') ?>>Notify only when a new login</option>
                            <option value="yes" <?= ($new_logic == 'yes' ? ' selected="selected"' : '') ?>>Mobile and PC limit logins</option>
                          </select>
                        </td>
                      </tr>


                      <tr>
                        <th scope="row"><label for="<?php echo $this->prefix("disable_wp_login"); ?>"><?php _e('Disable this on wp login.', 'gloo'); ?></label></th>
                        <td>
                          <?php $disable_wp_login = $this->get_option('disable_wp_login'); ?>
                          <select name="<?php echo $this->prefix("disable_wp_login"); ?>" id="<?php echo $this->prefix("disable_wp_login"); ?>" class="">
                            <option value="no" <?= ($disable_wp_login == 'no' ? ' selected="selected"' : '') ?>>No</option>
                            <option value="yes" <?= ($disable_wp_login == 'yes' ? ' selected="selected"' : '') ?>>Yes</option>
                          </select>
                        </td>
                      </tr>


                      <?php

                      echo $this->displayField('email_subject');
                      ?>
                    </tbody>
                  </table>
                  <table id="new_logic_table" class="form-table">
                    <tbody>
                      <?php echo $this->displayField('on_failure_send_email_to'); ?>
                      <tr>
                        <th scope="row">
                          <label for="<?php echo $this->prefix("email_message"); ?>"><?php _e('Email Message', 'gloo'); ?><br />
                            <small>
                              <?php _e('Placeholders:', 'gloo'); ?><br />
                              {username}<br />
                              {email}<br />
                            </small>
                          </label>
                        </th>
                        <td><textarea name="<?php echo $this->prefix("email_message_new"); ?>" id="<?php echo $this->prefix("email_message_new"); ?>" cols="30" rows="20" style="width:600px; max-width:100%;"><?php echo get_option($this->prefix("email_message_new")); ?></textarea></td>
                      </tr>
                      <tr>
                        <th scope="row"><label for="<?php echo $this->prefix("allowed_mobile_devices"); ?>"><?php _e('Allowed Mobile Devices', 'gloo'); ?></label></th>
                        <td>
                          <select name="<?php echo $this->prefix("allowed_mobile_devices"); ?>" id="<?php echo $this->prefix("allowed_mobile_devices"); ?>" class="">
                            <?php
                            $selected_countries = $this->get_option('allowed_mobile_devices');
                            for ($i = 1; $i <= 10; $i++) {
                              $selected_value = '';
                              if ($i == $selected_countries)
                                $selected_value = ' selected="selected"';
                              echo '<option value="' . $i . '"' . $selected_value . '>' . $i . '</option>';
                            }
                            ?>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <th scope="row"><label for="<?php echo $this->prefix("allowed_desktop_devices"); ?>"><?php _e('Allowed Desktop Devices', 'gloo'); ?></label></th>
                        <td>
                          <select name="<?php echo $this->prefix("allowed_desktop_devices"); ?>" id="<?php echo $this->prefix("allowed_desktop_devices"); ?>" class="">
                            <?php
                            $selected_countries = $this->get_option('allowed_desktop_devices');
                            for ($i = 1; $i <= 10; $i++) {
                              $selected_value = '';
                              if ($i == $selected_countries)
                                $selected_value = ' selected="selected"';
                              echo '<option value="' . $i . '"' . $selected_value . '>' . $i . '</option>';
                            }
                            ?>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <th scope="row"><label for="<?php echo $this->prefix("limit_admin"); ?>"><?php _e('Limit Admin', 'gloo'); ?></label></th>
                        <td>
                          <?php $limit_admin = $this->get_option('limit_admin'); ?>
                          <select name="<?php echo $this->prefix("limit_admin"); ?>" id="<?php echo $this->prefix("limit_admin"); ?>" class="">
                            <option value="no" <?= ($limit_admin == 'no' ? ' selected="selected"' : '') ?>>No</option>
                            <option value="yes" <?= ($limit_admin == 'yes' ? ' selected="selected"' : '') ?>>Yes</option>
                          </select>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <table id="old_logic_table" class="form-table">
                    <tbody>
                      <tr>
                        <th scope="row"><label for="<?php echo $this->prefix("allowed_devices"); ?>"><?php _e('Allowed Devices', 'gloo_for_elementor'); ?></label></th>
                        <td>
                          <select name="<?php echo $this->prefix("allowed_devices"); ?>" id="<?php echo $this->prefix("allowed_devices"); ?>" class="">
                            <?php
                            $selected_countries = $this->get_option('allowed_devices');
                            for ($i = 1; $i <= 10; $i++) {
                              $selected_value = '';
                              if ($i == $selected_countries)
                                $selected_value = ' selected="selected"';
                              echo '<option value="' . $i . '"' . $selected_value . '>' . $i . '</option>';
                            }
                            ?>
                          </select>
                        </td>
                      </tr>
                      <?php
                      echo $this->displayField('login_page_url');
                      echo $this->displayField('from_name');
                      echo $this->displayField('from_email');
                      ?>
                      <tr>
                        <th scope="row">
                          <label for="<?php echo $this->prefix("email_message"); ?>"><?php _e('Email Message', 'gloo'); ?><br />
                            <small>
                              <?php _e('Placeholders:', 'gloo'); ?><br />
                              {login_page_url}<br />
                              {approved_devices}<br />
                              {allowed_devices}<br />
                            </small>
                          </label>
                        </th>
                        <td><textarea name="<?php echo $this->prefix("email_message"); ?>" id="<?php echo $this->prefix("email_message"); ?>" cols="30" rows="20" style="width:600px; max-width:100%;"><?php echo get_option($this->prefix("email_message")); ?></textarea></td>
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
  }


  /******************************************/
  /***** input_handle function start from here *********/
  /******************************************/
  public function input_handle()
  {

    if (isset($_GET['page']) && $_GET['page'] === $this->prefix) {
      if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce']) && isset($_POST[$this->prefix('page_update_setting')])) {
        foreach ($this->pageFields as $key => $inputField) {
          if (in_array($key, $this->pageFieldsSkipSaving))
            continue;
          if (isset($_POST[$this->prefix($key)])) {
            $value = sanitize_text_field($_POST[$this->prefix($key)]);
            if ($value)
              $this->set_option($key, $value);
            else
              $this->set_option($key, '');
          }
        }



        if (isset($_POST[$this->prefix('email_message')])) {
          $value = wptexturize(otw_sanitize_textarea($_POST[$this->prefix('email_message')], true));
          if ($value)
            update_option($this->prefix('email_message'), $value);
          else
            update_option($this->prefix('email_message'), "");
        }

        if (isset($_POST[$this->prefix('email_message_new')])) {
          $value = wptexturize(otw_sanitize_textarea($_POST[$this->prefix('email_message_new')], true));
          if ($value)
            update_option($this->prefix('email_message_new'), $value);
          else
            update_option($this->prefix('email_message_new'), "");
        }

        add_action('admin_notices', [$this, 'admin_notices']);
      }
    } // if isset page end here

  } // input handle function end here


  public function displayField($field_key)
  {
    $output = '';
    if (isset($this->pageFields[$field_key])) {
      $placeholder = '';
      if ($this->pageFields[$field_key]['placeholder'])
        $placeholder = ' placeholder="' . $this->pageFields[$field_key]['placeholder'] . '"';
      $output = '<tr>
        <th scope="row"><label for="' . $this->prefix($field_key) . '">' . $this->pageFields[$field_key]['label'] . '</label></th>
        <td><input type="text" name="' . $this->prefix($field_key) . '" id="' . $this->prefix($field_key) . '" value="' . $this->get_option($field_key) . '" class="regular-text" style="width:600px; max-width:100%;"' . $placeholder . '></td>
        </tr>';
    }
    return $output;
  }


  public function pageURL($param = array())
  {
    $pageURL = add_query_arg($param, get_admin_url(null, 'admin.php?page=' . $this->prefix));
    return $pageURL;
  }
}// class end here
