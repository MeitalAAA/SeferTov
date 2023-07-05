<?php

/**
 * Coupon import export
 *
 *
 * @link              https://www.webtoffee.com/
 * @since             1.0.0
 * @package           Wt_Import_Export_For_Woo
 *
 * @wordpress-plugin
 * Plugin Name:       Coupon Import Export for WooCommerce Add-on
 * Plugin URI:        https://www.webtoffee.com/product/woocommerce-import-export-suite/
 * Description:       Coupon Import Export Add-on for WooCommerce
 * Version:           1.1.2
 * Author:            WebToffee
 * Author URI:        https://www.webtoffee.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wt-import-export-for-woo
 * Domain Path:       /languages
 * WC tested up to:   6.9
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* Plugin page links */
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wt_iew_plugin_action_links_coupon');

function wt_iew_plugin_action_links_coupon($links)
{
	if(defined('WT_IEW_PLUGIN_ID')) /* main plugin is available */
	{
		$links[] = '<a href="'.admin_url('admin.php?page='.WT_IEW_PLUGIN_ID).'">'.__('Settings', 'wt-import-export-for-woo').'</a>';
	}

	$links[] = '<a href="https://www.webtoffee.com/how-to-import-and-export-woocommerce-coupons/" target="_blank">'.__('Documentation', 'wt-import-export-for-woo').'</a>';
	$links[] = '<a href="https://www.webtoffee.com/support/" target="_blank">'.__('Support', 'wt-import-export-for-woo').'</a>';
	return $links;
}

/**
* Missing plugins warning.
*/
add_action( 'admin_notices',  'wt_missing_plugins_warning');
if(!function_exists('wt_missing_plugins_warning')){
    function wt_missing_plugins_warning() {
        if (!get_option('wt_iew_is_active')) {            
            /* Display the notice*/
            $class = 'notice notice-error';                        
            $message = sprintf(__('The <b>WebToffee Import/Export wrapper plugin</b> should be activated in order to import/export any of the post types supported via <b>WebToffee add-ons(Product/Reviews, User, Order/Coupon/Subscription)</b>.
            Go to <a href="%s" target="_blank">My accounts->API Downloads</a> to download and activate the wrapper.  If already installed, activate the wrapper plugin from under <a href="%s" target="_blank">Plugins</a>.', 'wt-import-export-for-woo'),'https://www.webtoffee.com/my-account/my-api-downloads/',admin_url('plugins.php?s=Import%20Export%20for%20WooCommerce'));
            printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), ( $message ) ); 
                                
        }
    }
}

register_activation_hook( __FILE__, 'wt_missing_plugins_warning_on_activation_coupon' );
function wt_missing_plugins_warning_on_activation_coupon() {
    if( !get_option('wt_iew_is_active')){
        set_transient( 'wt_missing_plugins_warning_on_activation_coupon', true, 5 );
    }
}
add_action( 'admin_notices',  'wt_missing_plugins_warning_coupon',1);
function wt_missing_plugins_warning_coupon(){
    /* Check transient, if available display the notice on plugin activation */
    if( get_transient( 'wt_missing_plugins_warning_on_activation_coupon' ) ){

        $class = 'notice notice-error';  
        $post_type = 'coupon';
        $message = sprintf(__('<b>%s</b> has been activated. However you need to install and activate the <b>WebToffee wrapper plugin</b> also to start export/import of %s.
        Go to <a href="%s" target="_blank">My accounts->API Downloads</a> to download and activate the wrapper. If already installed activate the wrapper plugin from under <a href="%s" target="_blank">Plugins</a>.', 'wt-import-export-for-woo'), ucfirst($post_type) .' import export', $post_type.'s', 'https://www.webtoffee.com/my-account/my-api-downloads/',admin_url('plugins.php?s=Import%20Export%20for%20WooCommerce'));
        printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), ( $message ) );                     

        /* Delete transient, only display this notice once. */
        delete_transient( 'wt_missing_plugins_warning_on_activation_coupon' );
    }   
}

add_action( 'wt_coupon_addon_help_content', 'wt_coupon_import_export_help_content' );

function wt_coupon_import_export_help_content() {
	if ( defined( 'WT_IEW_PLUGIN_ID' ) ) {
		?>
			<li>
				<img src="<?php echo WT_IEW_PLUGIN_URL; ?>assets/images/sample-csv.png">
				<h3><?php _e( 'Sample Coupon CSV', 'wt-import-export-for-woo'); ?></h3>
				<p><?php _e( 'Familiarize yourself with the sample CSV.', 'wt-import-export-for-woo'); ?></p>
				<a target="_blank" href="https://www.webtoffee.com/wp-content/uploads/2016/09/Coupon_Sample_CSV.csv" class="button button-primary">
				<?php _e( 'Get Coupon CSV', 'wt-import-export-for-woo'); ?>        
				</a>
			</li>
		<?php
	}
}

/**
 * Add Export to CSV link in coupon listing page near the filter button.
 * 
 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
 */
function wt_ier_export_csv_linkin_coupon_listing_page($which) {

	$currentScreen = get_current_screen();

	if ( 'edit-shop_coupon' === $currentScreen->id) {
		echo '<a target="_blank" href="' . admin_url('admin.php?page=wt_import_export_for_woo_export&wt_to_export=coupon') . '" class="button" >' . __('Export to CSV') . ' </a>';
	}
}

add_filter('manage_posts_extra_tablenav', 'wt_ier_export_csv_linkin_coupon_listing_page');
