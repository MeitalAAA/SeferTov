<?php

namespace Gloo\Modules\DataLayer_Connector;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'datalayer_connector';

	/**
	 * @var Conditions\Manager
	 */
	public $conditions = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {

		add_filter( 'gloo/modules/interactor/enable_variables', '__return_true' );
		require gloo()->modules_path( 'datalayer-connector/inc/settings.php' );
		new Settings();

		// settings page
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', [ $this, 'gloo_interactor_settings_init' ] );


	}

	public function gloo_interactor_settings_init() {
		add_settings_section(
			$this->slug . '_settings_section',
			__( 'DataLayer Settings', 'gloo_for_elementor' ),
			'',
			$this->slug
		);

		add_settings_field(
			$this->slug . "_js",
			__( 'Enqueue GTM Script', 'gloo_for_elementor' ),
			[ $this, 'enqueue_gtm' ],
			$this->slug,
			$this->slug . '_settings_section',
		);
		add_settings_field(
			$this->slug . "_key",
			__( 'GTM Key', 'gloo_for_elementor' ),
			[ $this, 'gtm_key' ],
			$this->slug,
			$this->slug . '_settings_section',
		);
		register_setting( $this->slug, $this->slug . '_js' );
		register_setting( $this->slug, $this->slug . '_key' );

	}

	public function gtm_key() {
		$check = get_option( $this->slug . '_key' );
		echo "<input type='text' name='" . $this->slug . "_key' value='$check' placeholder='GTM-XXXX'>";
	}

	public function enqueue_gtm() {
		$check = get_option( $this->slug . '_js' ) ? "checked='checked'" : "";
		echo "<input type='checkbox' name='" . $this->slug . "_js' value='1' $check>";

	}

	public function admin_menu() {

		/* add sub menu in our wordpress dashboard main menu */
		add_submenu_page(
			null, // hide from menu
			__( 'DataLayer', 'gloo_for_elementor' ),
			__( 'DataLayer', 'gloo_for_elementor' ),
			'manage_options',
			$this->slug,
			array( $this, 'add_submenu_page' )
		);

	}

	public function prefix( $string = '' ) {
		return $this->slug . '_' . $string;
	}

	public function add_submenu_page() {
		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' );
		?>
        <form method="POST" action="options.php">
			<?php
			settings_fields( $this->slug );
			do_settings_sections( $this->slug );
			submit_button();
			?>
        </form>
		<?php
		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' );
	}

	/**
	 * Returns the instance.
	 *
	 * @return Module
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
