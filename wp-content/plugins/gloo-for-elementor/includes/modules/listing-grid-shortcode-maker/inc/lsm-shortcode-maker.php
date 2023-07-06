<?php
namespace Gloo\Modules\Listing_Grid_Shortcode_Maker;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Plugin {

	private static $post_ids = '';


	/**
	 * Instance
	 *
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Plugin An instance of the class.
	 * @access public
	 *
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'lsm_add_custom_shortcodes' ] );
		add_action( 'admin_menu', [ $this, 'add_plugin_page' ], 11 );
		add_action( 'admin_init', [ $this, 'page_init' ] );

		// Register Custom Post Type
		add_action( 'init', [ $this, 'gloo_lsm_custom_post_type' ], 0 );

		// add meta box to posts
		add_action( 'add_meta_boxes', [ $this, 'lsm_add_meta_boxes' ] );

		// save
		add_action( 'save_post', [ $this, 'lsm_save_post' ] );

		// redirect
		add_filter( 'redirect_post_location', [ $this, 'gloo_lsm_redirect_post_location' ] );

		// admin scrips

	}

	public function gloo_lsm_redirect_post_location( $location ) {

		if ( 'gloo_lsm' == get_post_type() ) {

			/* Custom code for gloo_lsm post type. */

			if ( isset( $_POST['save'] ) || isset( $_POST['publish'] ) ) {
				return admin_url( "admin.php?page=listing_shortcode" );
			}

		}

		return $location;
	}

	public function lsm_save_post() {
		global $post;

		// auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST["lsm_elementor_template"] ) ) {
			$value = $_POST["lsm_elementor_template"];
			update_post_meta( $post->ID, 'lsm_elementor_template', $value );
		}

		if ( isset( $_POST["lsm_listing_shortcode"] ) ) {
			$value = $_POST["lsm_listing_shortcode"];
			update_post_meta( $post->ID, 'lsm_listing_shortcode', $value );
		}

	}

	public function lsm_add_meta_boxes() {

		global $post;

		if ( empty( $post ) ) {
			return;
		}

		add_meta_box(
			'dnm_elementor_choice', // $id
			'Listing Shortcode', // $title
			[ $this, 'lsm_show_meta_boxes' ], // $callback
			'gloo_lsm',
			'normal' // $context
		);

	}

	public function lsm_show_meta_boxes() {
		echo "<p>Elementor Template: </p>";

		$args = array(
			'numberposts' => - 1,
			'post_type'   => 'elementor_library'
		);

		$template_posts = get_posts( $args );
		$options        = '';
		if ( $template_posts ) {
			$selected_id = get_post_meta( get_the_ID(), 'lsm_elementor_template', true );
			foreach ( $template_posts as $template_post ) {
				$title       = get_the_title( $template_post->ID );
				$is_selected = $selected_id == $template_post->ID ? 'selected' : '';
				$options     .= "<option value='$template_post->ID' $is_selected>$title</option>";
			}
			wp_reset_postdata();
		}

		if ( $options ) {
			echo "<select name='lsm_elementor_template'><option value=''>" . __( 'Disabled', 'gloo_for_elementor' ) . "</option>$options</select>";
		}

		echo "<p>Shortcode Name: </p>";
		printf(
			'<input type="text" id="listing_shortcode" name="lsm_listing_shortcode" value="%s" />',
			get_post_meta( get_the_ID(), 'lsm_listing_shortcode', true )
		);

	}

	public function gloo_lsm_custom_post_type() {

		$labels = array(
			'name'           => _x( 'Listing Shortcode', 'Post Type General Name', 'gloo_for_elementor' ),
			'singular_name'  => _x( 'Listing Shortcodes', 'Post Type Singular Name', 'gloo_for_elementor' ),
			'menu_name'      => __( 'Listing Shortcode Maker', 'gloo_for_elementor' ),
			'name_admin_bar' => __( 'Listing Shortcode Maker', 'gloo_for_elementor' ),
		);
		$args   = array(
			'label'               => __( 'Listing Shortcode Maker', 'gloo_for_elementor' ),
			'description'         => __( 'Listing Shortcode Maker', 'gloo_for_elementor' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		register_post_type( 'gloo_lsm', $args );

	}

	public function lsm_add_custom_shortcodes() {
		$args       = array(
			'numberposts' => - 1,
			'post_type'   => 'gloo_lsm'
		);
		$shortcodes = get_posts( $args );

		if(!empty($shortcodes)) {

			foreach ( $shortcodes as $shortcode ) {

				$shotcode_name      = get_post_meta( $shortcode->ID, 'lsm_listing_shortcode', true );
				$elementor_template = get_post_meta( $shortcode->ID, 'lsm_elementor_template', true );

				if(!empty($shotcode_name) && !empty($elementor_template)) {

					add_shortcode( $shotcode_name, function ( $atts ) use ( $elementor_template, $shotcode_name ) {

					if ( ! isset( $atts['id'] ) || ! $elementor_template || ! $shotcode_name ) {
						return;
					}

					self::$post_ids = $atts['id'];
					add_filter( 'jet-engine/listings/macros-list', function ( $macros_list ) use ( $shotcode_name ) {
						if ( isset( $macros_list[ $shotcode_name ] ) ) {
							return $macros_list;
						}

						$macros_list[ $shotcode_name ] = [ $this, 'get_posts' ];

						return $macros_list;
					}, 10, 1 );


					// elementor template
					return do_shortcode( "[elementor-template id='$elementor_template']" );
					} );
				}
			}
		}
	}


	public function get_posts() {
		return self::$post_ids;
	}


	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		add_submenu_page(
			null, // hide from menu
			'Listing SC Maker',
			'Listing SC Maker',
			'manage_options',
			'listing_shortcode',
			[ $this, 'create_admin_page' ]
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$args = array(
			'numberposts' => - 1,
			'post_type'   => 'gloo_lsm'
		);

		$template_posts = get_posts( $args );
		
		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); ?>

            <h1>Listing Shortcode</h1>
			<?php
			$new_post_link = admin_url( 'post-new.php?post_type=gloo_lsm' );
			if ( $template_posts ):
				$html = '';

				foreach ( $template_posts as $template_post ) {
					$title     = get_the_title( $template_post->ID );
					$edit_link = get_edit_post_link( $template_post->ID );
					$shortcode = get_post_meta( $template_post->ID, 'lsm_listing_shortcode', true );
					$html      .= "<div class='gloo-lsm-item'>
                    <div><b>Name</b>$title</div>
                    <div><b>Macro</b><span class='shortcode'>%$shortcode%</span></div>
                    <div><b>Shortcode</b><span class='shortcode'>[$shortcode id=\"123, 1234\"]</span></div>
                   <a href='$edit_link'>Edit</a>
                    </div>";
				}
			else:
				echo "<div>No listings yet, click the button below to add one!</div>";
			endif;
			echo "<div class='gloo-lms-wrap'>$html <a class='add-new' href='$new_post_link'>Add New</a></div>";
		
		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' ); 
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'listing_shortcode_group', // Option group
			'listing_shortcode', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'listing_shortcode_section_id', // ID
			'Listing Shortcode', // Title
			array( $this, 'print_section_info' ), // Callback
			'listing_shortcode' // Page
		);


		add_settings_field(
			'listing_shortcode',
			'Elementor template shortcode of listing grid',
			array( $this, 'listing_shortcode_callback' ),
			'listing_shortcode',
			'listing_shortcode_section_id'
		);

		add_settings_field(
			'listing_shortcode_name',
			'Shortcode Name',
			array( $this, 'listing_shortcode_name_callback' ),
			'listing_shortcode',
			'listing_shortcode_section_id'
		);
	}


}


// Instantiate Plugin Class
Plugin::instance();