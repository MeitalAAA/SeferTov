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
    add_submenu_page(
      null, // hide from menu
      __('Autocomplete fields', 'gloo'), 
      __('Autocomplete fields', 'gloo'), 
      'manage_options', 
      $this->prefix, 
      array($this,'add_submenu_page') 
    );
    
  }

  /******************************************/
  /***** add_submenu_page_bboptions function start from here *********/
  /******************************************/
  public function add_submenu_page(){
    $countries = array
    (
      'AF' => 'Afghanistan',
      'AX' => 'Aland Islands',
      'AL' => 'Albania',
      'DZ' => 'Algeria',
      'AS' => 'American Samoa',
      'AD' => 'Andorra',
      'AO' => 'Angola',
      'AI' => 'Anguilla',
      'AQ' => 'Antarctica',
      'AG' => 'Antigua And Barbuda',
      'AR' => 'Argentina',
      'AM' => 'Armenia',
      'AW' => 'Aruba',
      'AU' => 'Australia',
      'AT' => 'Austria',
      'AZ' => 'Azerbaijan',
      'BS' => 'Bahamas',
      'BH' => 'Bahrain',
      'BD' => 'Bangladesh',
      'BB' => 'Barbados',
      'BY' => 'Belarus',
      'BE' => 'Belgium',
      'BZ' => 'Belize',
      'BJ' => 'Benin',
      'BM' => 'Bermuda',
      'BT' => 'Bhutan',
      'BO' => 'Bolivia',
      'BA' => 'Bosnia And Herzegovina',
      'BW' => 'Botswana',
      'BV' => 'Bouvet Island',
      'BR' => 'Brazil',
      'IO' => 'British Indian Ocean Territory',
      'BN' => 'Brunei Darussalam',
      'BG' => 'Bulgaria',
      'BF' => 'Burkina Faso',
      'BI' => 'Burundi',
      'KH' => 'Cambodia',
      'CM' => 'Cameroon',
      'CA' => 'Canada',
      'CV' => 'Cape Verde',
      'KY' => 'Cayman Islands',
      'CF' => 'Central African Republic',
      'TD' => 'Chad',
      'CL' => 'Chile',
      'CN' => 'China',
      'CX' => 'Christmas Island',
      'CC' => 'Cocos (Keeling) Islands',
      'CO' => 'Colombia',
      'KM' => 'Comoros',
      'CG' => 'Congo',
      'CD' => 'Congo, Democratic Republic',
      'CK' => 'Cook Islands',
      'CR' => 'Costa Rica',
      'CI' => 'Cote D\'Ivoire',
      'HR' => 'Croatia',
      'CU' => 'Cuba',
      'CY' => 'Cyprus',
      'CZ' => 'Czech Republic',
      'DK' => 'Denmark',
      'DJ' => 'Djibouti',
      'DM' => 'Dominica',
      'DO' => 'Dominican Republic',
      'EC' => 'Ecuador',
      'EG' => 'Egypt',
      'SV' => 'El Salvador',
      'GQ' => 'Equatorial Guinea',
      'ER' => 'Eritrea',
      'EE' => 'Estonia',
      'ET' => 'Ethiopia',
      'FK' => 'Falkland Islands (Malvinas)',
      'FO' => 'Faroe Islands',
      'FJ' => 'Fiji',
      'FI' => 'Finland',
      'FR' => 'France',
      'GF' => 'French Guiana',
      'PF' => 'French Polynesia',
      'TF' => 'French Southern Territories',
      'GA' => 'Gabon',
      'GM' => 'Gambia',
      'GE' => 'Georgia',
      'DE' => 'Germany',
      'GH' => 'Ghana',
      'GI' => 'Gibraltar',
      'GR' => 'Greece',
      'GL' => 'Greenland',
      'GD' => 'Grenada',
      'GP' => 'Guadeloupe',
      'GU' => 'Guam',
      'GT' => 'Guatemala',
      'GG' => 'Guernsey',
      'GN' => 'Guinea',
      'GW' => 'Guinea-Bissau',
      'GY' => 'Guyana',
      'HT' => 'Haiti',
      'HM' => 'Heard Island & Mcdonald Islands',
      'VA' => 'Holy See (Vatican City State)',
      'HN' => 'Honduras',
      'HK' => 'Hong Kong',
      'HU' => 'Hungary',
      'IS' => 'Iceland',
      'IN' => 'India',
      'ID' => 'Indonesia',
      'IR' => 'Iran, Islamic Republic Of',
      'IQ' => 'Iraq',
      'IE' => 'Ireland',
      'IM' => 'Isle Of Man',
      'IL' => 'Israel',
      'IT' => 'Italy',
      'JM' => 'Jamaica',
      'JP' => 'Japan',
      'JE' => 'Jersey',
      'JO' => 'Jordan',
      'KZ' => 'Kazakhstan',
      'KE' => 'Kenya',
      'KI' => 'Kiribati',
      'KR' => 'Korea',
      'KW' => 'Kuwait',
      'KG' => 'Kyrgyzstan',
      'LA' => 'Lao People\'s Democratic Republic',
      'LV' => 'Latvia',
      'LB' => 'Lebanon',
      'LS' => 'Lesotho',
      'LR' => 'Liberia',
      'LY' => 'Libyan Arab Jamahiriya',
      'LI' => 'Liechtenstein',
      'LT' => 'Lithuania',
      'LU' => 'Luxembourg',
      'MO' => 'Macao',
      'MK' => 'Macedonia',
      'MG' => 'Madagascar',
      'MW' => 'Malawi',
      'MY' => 'Malaysia',
      'MV' => 'Maldives',
      'ML' => 'Mali',
      'MT' => 'Malta',
      'MH' => 'Marshall Islands',
      'MQ' => 'Martinique',
      'MR' => 'Mauritania',
      'MU' => 'Mauritius',
      'YT' => 'Mayotte',
      'MX' => 'Mexico',
      'FM' => 'Micronesia, Federated States Of',
      'MD' => 'Moldova',
      'MC' => 'Monaco',
      'MN' => 'Mongolia',
      'ME' => 'Montenegro',
      'MS' => 'Montserrat',
      'MA' => 'Morocco',
      'MZ' => 'Mozambique',
      'MM' => 'Myanmar',
      'NA' => 'Namibia',
      'NR' => 'Nauru',
      'NP' => 'Nepal',
      'NL' => 'Netherlands',
      'AN' => 'Netherlands Antilles',
      'NC' => 'New Caledonia',
      'NZ' => 'New Zealand',
      'NI' => 'Nicaragua',
      'NE' => 'Niger',
      'NG' => 'Nigeria',
      'NU' => 'Niue',
      'NF' => 'Norfolk Island',
      'MP' => 'Northern Mariana Islands',
      'NO' => 'Norway',
      'OM' => 'Oman',
      'PK' => 'Pakistan',
      'PW' => 'Palau',
      'PS' => 'Palestinian Territory, Occupied',
      'PA' => 'Panama',
      'PG' => 'Papua New Guinea',
      'PY' => 'Paraguay',
      'PE' => 'Peru',
      'PH' => 'Philippines',
      'PN' => 'Pitcairn',
      'PL' => 'Poland',
      'PT' => 'Portugal',
      'PR' => 'Puerto Rico',
      'QA' => 'Qatar',
      'RE' => 'Reunion',
      'RO' => 'Romania',
      'RU' => 'Russian Federation',
      'RW' => 'Rwanda',
      'BL' => 'Saint Barthelemy',
      'SH' => 'Saint Helena',
      'KN' => 'Saint Kitts And Nevis',
      'LC' => 'Saint Lucia',
      'MF' => 'Saint Martin',
      'PM' => 'Saint Pierre And Miquelon',
      'VC' => 'Saint Vincent And Grenadines',
      'WS' => 'Samoa',
      'SM' => 'San Marino',
      'ST' => 'Sao Tome And Principe',
      'SA' => 'Saudi Arabia',
      'SN' => 'Senegal',
      'RS' => 'Serbia',
      'SC' => 'Seychelles',
      'SL' => 'Sierra Leone',
      'SG' => 'Singapore',
      'SK' => 'Slovakia',
      'SI' => 'Slovenia',
      'SB' => 'Solomon Islands',
      'SO' => 'Somalia',
      'ZA' => 'South Africa',
      'GS' => 'South Georgia And Sandwich Isl.',
      'ES' => 'Spain',
      'LK' => 'Sri Lanka',
      'SD' => 'Sudan',
      'SR' => 'Suriname',
      'SJ' => 'Svalbard And Jan Mayen',
      'SZ' => 'Swaziland',
      'SE' => 'Sweden',
      'CH' => 'Switzerland',
      'SY' => 'Syrian Arab Republic',
      'TW' => 'Taiwan',
      'TJ' => 'Tajikistan',
      'TZ' => 'Tanzania',
      'TH' => 'Thailand',
      'TL' => 'Timor-Leste',
      'TG' => 'Togo',
      'TK' => 'Tokelau',
      'TO' => 'Tonga',
      'TT' => 'Trinidad And Tobago',
      'TN' => 'Tunisia',
      'TR' => 'Turkey',
      'TM' => 'Turkmenistan',
      'TC' => 'Turks And Caicos Islands',
      'TV' => 'Tuvalu',
      'UG' => 'Uganda',
      'UA' => 'Ukraine',
      'AE' => 'United Arab Emirates',
      'GB' => 'United Kingdom',
      'US' => 'United States',
      'UM' => 'United States Outlying Islands',
      'UY' => 'Uruguay',
      'UZ' => 'Uzbekistan',
      'VU' => 'Vanuatu',
      'VE' => 'Venezuela',
      'VN' => 'Viet Nam',
      'VG' => 'Virgin Islands, British',
      'VI' => 'Virgin Islands, U.S.',
      'WF' => 'Wallis And Futuna',
      'EH' => 'Western Sahara',
      'YE' => 'Yemen',
      'ZM' => 'Zambia',
      'ZW' => 'Zimbabwe',
    );

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
                    <h3 class="hndle ui-sortable-handle"><span><?php _e('Autocomplete Address Fields', 'gloo_for_elementor'); ?></span></h3>
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
                            <label for="<?php echo $this->prefix("input_element_class"); ?>"><?php _e('Input element class name', 'gloo'); ?></label><br>
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
                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("supported_countries"); ?>"><?php _e('Supported Countries', 'gloo_for_elementor'); ?></label></th>
                          <td>
                            <select name="<?php echo $this->prefix("supported_countries"); ?>[]" id="<?php echo $this->prefix("supported_countries"); ?>" class="gloo_select_two_input" multiple>
                              <?php 
                              $selected_countries = SerializeStringToArray($this->get_option('supported_countries'));
                              foreach($countries as $key=>$country){
                                $selected_country = '';
                                if(in_array($key, $selected_countries))
                                  $selected_country = ' selected="selected"';
                                echo '<option value="'.$key.'"'.$selected_country.'>'.$country.'</option>';
                              }
                              ?>
                            </select>
                          </td>
                        </tr>

                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("enable_regions_only"); ?>"><?php _e('Regions Only', 'gloo_for_elementor'); ?></label></th>
                          <td>
                            <input type="checkbox" name="<?php echo $this->prefix("enable_regions_only"); ?>" id="<?php echo $this->prefix("enable_regions_only"); ?>" <?php if($this->get_option('enable_regions_only')){ echo 'checked="checked"'; } ?>>
                          </td>
                        </tr>
                        <tr>
                          <th scope="row"><label for="<?php echo $this->prefix("api_lib_lang"); ?>"><?php _e('Language', 'gloo_for_elementor'); ?></label></th>
                          <td><input type="text" name="<?php echo $this->prefix("api_lib_lang"); ?>" id="<?php echo $this->prefix("api_lib_lang"); ?>" value="<?php echo $this->get_option('api_lib_lang'); ?>" class="regular-text" placeholder="en" style="width: 150px;"></td>
                        </tr>
                        <?php
                        
                        /*<tr>
                          <th scope="row">
                            <label for="<?php echo $this->prefix("load_on_pages"); ?>">
                              <?php _e('Load on Pages ', 'gloo'); ?><br>
                              <small><?php _e('Page IDs seperated by comma, leave blank if you want to load the javascript of this plugin on all pages.', 'gloo'); ?></small>
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
    include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' );
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

        if(isset($_POST[$this->prefix('enable_regions_only')]) && $_POST[$this->prefix('enable_regions_only')] == 'on'){
          $value = sanitize_text_field($_POST[$this->prefix('enable_regions_only')]);
          $this->set_option('enable_regions_only', $value);
        }else{
          $this->set_option('enable_regions_only', '');
        }

        if(isset($_POST[$this->prefix('api_lib_lang')])){
          $value = sanitize_text_field($_POST[$this->prefix('api_lib_lang')]);
          $this->set_option('api_lib_lang', $value);
        }

        if(isset($_POST[$this->prefix('supported_countries')]) && is_array($_POST[$this->prefix('supported_countries')]) && count($_POST[$this->prefix('supported_countries')]) >= 1){
          $this->set_option('supported_countries', ArrayToSerializeString($_POST[$this->prefix('supported_countries')]));
        }
        else
          $this->set_option('supported_countries', ArrayToSerializeString(array()));


        add_action( 'admin_notices', [ $this, 'adminNotices' ] );

      }
      

    } // if isset page end here

  } // input handle function end here

}// class end here
