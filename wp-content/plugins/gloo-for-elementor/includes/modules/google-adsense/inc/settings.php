<?php
namespace Gloo\Modules\Google_Adsense_Widget;

/**
 * Class Settings
 *
 */
class Settings {
	private $options;

	public function __construct() {
		add_action( 'wp_head', [ $this, 'add_adsense_code_header'] );
		add_action( 'admin_menu', [ $this, 'add_plugin_page' ], 11 );
		add_action( 'admin_init', [ $this, 'page_init' ] );

	}
 
	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		add_submenu_page(
			null, // null hide from menu
			'Google Adsense',
			'Google Adsense',
			'manage_options',
			'google-adsense',
			[ $this, 'create_admin_page' ]
		);
		
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		
		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); 
		$this->options = get_option( 'gloo_adsense' ); ?>
 		
		<form method="POST" action="options.php" class="gloo-settings">
			<?php
			settings_fields( 'adsense_setting' );
			do_settings_sections( 'adsense-setting' );

			echo '<div class="gloo-adsense-code">
			<h2>Adsense Example Code </h2>
			&lt;script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"&gt;&lt;/script&gt;<br>
			<br>
			&lt;ins class="adsbygoogle" style="display:block"&gt;<br>
			<mark>data-ad-client="ca-pub-2049373471280744"<br></mark>
			data-ad-slot="5830668103"<br>
			data-ad-format="auto"<br>
			data-full-width-responsive="true"<br>
			&lt;/ins&gt;
			</div>';
			
			submit_button(); ?>
        </form>

		<?php include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' ); 
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
            'adsense_setting', // Option group
            'gloo_adsense'// Option name
         );

        add_settings_section(
            'setting_section_basic', // ID
            'Google Adsense Settings', // Title
            '', // Callback
            'adsense-setting' // Page
        );  

        add_settings_field(
            'data_ad_client', // ID
            'Data Ad Client', // Title 
            array( $this, 'data_ad_client_callback' ), // Callback
            'adsense-setting', // Page
            'setting_section_basic' // Section           
		);   
	}

    /** 
     * Get the settings option array and print one of its values
     */
    public function data_ad_client_callback()
    {
         printf(
            '<input type="text" id="data_ad_client" name="gloo_adsense[data_ad_client]" value="%s" />',
            isset( $this->options['data_ad_client'] ) ? esc_attr( $this->options['data_ad_client']) : ''
        );
	}
	
	public function adsense_script_callback()
    {
         printf(
            '<textarea id="adsense_header_script" name="gloo_adsense[adsense_header_script]">%s</textarea>',
            isset( $this->options['adsense_header_script'] ) ? esc_attr( $this->options['adsense_header_script']) : ''
        );
	}
	
	public function add_adsense_code_header() {
		
		echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';

	}

}
