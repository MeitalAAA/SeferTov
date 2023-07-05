<?php
namespace Gloo\Modules\Acf_Relation_Field_Macro;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Plugin {

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

		// init macros
		add_action( 'wp', [ $this, 'init_macros' ] );

		// add admin page
		add_action( 'admin_menu', [ $this, 'add_plugin_page' ], 11 );

		// Register Custom Post Type
		add_action( 'init', [ $this, 'add_post_type' ], 0 );

		// add meta box to posts
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		// save meta data
		add_action( 'save_post', [ $this, 'save_meta_data' ] );

		// redirect after saving
		add_filter( 'redirect_post_location', [ $this, 'redirect_post_location' ] );

		// custom columns
		add_filter( 'manage_gloo_acf_rb_posts_columns', [ $this, 'set_gloo_acf_rb_columns' ] );
		add_action( 'manage_gloo_acf_rb_posts_custom_column', [ $this, 'set_gloo_acf_rb_column_data' ], 10, 2 );

	}

	public function add_meta_boxes() {

		global $post;

		if ( empty( $post ) ) {
			return;
		}

		add_meta_box(
			'gloo_acf_rb', // $id
			'ACF Relationship Bridge for JetEngine', // $title
			[ $this, 'show_meta_boxes' ], // $callback
			'gloo_acf_rb',
			'normal' // $context
		);

	}

	public function show_meta_boxes() {

		echo "<p>Macro Name: </p>";
		printf(
			'<input type="text" id="gloo_acf_rb_macro" name="gloo_acf_rb_macro" value="%s" />',
			get_post_meta( get_the_ID(), 'gloo_acf_rb_macro', true )
		);

		echo "<p>Relationship Field: </p>";
		printf(
			'<input type="text" id="gloo_acf_rb_relationship" name="gloo_acf_rb_relationship" value="%s" />',
			get_post_meta( get_the_ID(), 'gloo_acf_rb_relationship', true )
		);

		echo "<p>Fallback to default query: </p>";
		printf(
			'<input type="checkbox" id="gloo_acf_rb_fallback" name="gloo_acf_rb_fallback" value="1" %s />',
			get_post_meta( get_the_ID(), 'gloo_acf_rb_fallback', true ) ? 'checked="checked"' : ''
		);
	}


	public function set_gloo_acf_rb_column_data( $column, $post_id ) {
		switch ( $column ) {
			case 'macro_name' :
				$macro_name = get_post_meta( $post_id, 'gloo_acf_rb_macro', true );
				echo $macro_name ? "%$macro_name%" : "";
				break;
			case 'acf_relation' :
				echo get_post_meta( $post_id, 'gloo_acf_rb_relationship', true );
				break;
		}
	}

	public function set_gloo_acf_rb_columns( $columns ) {
		$date_column = $columns['date'];
		unset( $columns['date'] );
		$columns['macro_name']   = __( 'Macro', 'gloo_for_elementor' );
		$columns['acf_relation'] = __( 'ACF Relation', 'gloo_for_elementor' );
		$columns['date']         = $date_column;

		return $columns;
	}

	public function redirect_post_location( $location ) {

		if ( 'gloo_acf_rb' == get_post_type() ) {

			/* Custom redirection for gloo_acf_rb post type. */

			if ( isset( $_POST['save'] ) || isset( $_POST['publish'] ) ) {
				return admin_url( "edit.php?post_type=gloo_acf_rb" );
			}

		}

		return $location;
	}

	public function save_meta_data() {
		global $post;
		
		// auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if(isset($post) && !empty($post)) {

			if ( isset( $_POST["gloo_acf_rb_macro"] ) ) {
				$value = sanitize_text_field( $_POST["gloo_acf_rb_macro"] );
				update_post_meta( $post->ID, 'gloo_acf_rb_macro', $value );
			}

			if ( isset( $_POST["gloo_acf_rb_relationship"] ) ) {
				$value = sanitize_text_field( $_POST["gloo_acf_rb_relationship"] );
				update_post_meta( $post->ID, 'gloo_acf_rb_relationship', $value );
			}

			$value = isset( $_POST["gloo_acf_rb_fallback"] ) ? 1 : 0;
			update_post_meta( $post->ID, 'gloo_acf_rb_fallback', $value );
		}
	}

	public function add_post_type() {

		$labels = array(
			'name'           => _x( 'ACF Relationship Bridge For JetEngine', 'ACF Relationship Bridge For JetEngine', 'gloo_for_elementor' ),
			'singular_name'  => _x( 'ACF Relationship Bridges For JetEngine', 'Post Type Singular Name', 'gloo_for_elementor' ),
			'menu_name'      => __( 'ACF Relationship Bridge For JetEngine', 'gloo_for_elementor' ),
			'name_admin_bar' => __( 'ACF Relationship Bridge For JetEngine', 'gloo_for_elementor' ),
		);
		$args   = array(
			'label'               => __( 'ACF Relationship Bridge For JetEngine', 'gloo_for_elementor' ),
			'description'         => __( 'ACF Relationship Bridge For JetEngine', 'gloo_for_elementor' ),
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
		register_post_type( 'gloo_acf_rb', $args );

	}

	public function init_macros() {
		$args          = array(
			'numberposts' => - 1,
			'post_type'   => 'gloo_acf_rb'
		);
		$custom_macros = get_posts( $args );

		if ( ! $custom_macros ) {
			return;
		}

		foreach ( $custom_macros as $custom_macro ) {
			$relationship_name = get_post_meta( $custom_macro->ID, 'gloo_acf_rb_relationship', true );
			$macro_name        = get_post_meta( $custom_macro->ID, 'gloo_acf_rb_macro', true );
			$fallback          = get_post_meta( $custom_macro->ID, 'gloo_acf_rb_fallback', true );

			// skip if one of the fields is missing
			if ( ! $relationship_name || ! $macro_name ) {
				continue;
			}

			if ( ! function_exists( 'get_field' ) ) {
				return;
			}

			$acf_id_prefix = '';
			if ( is_tax() ) {
				$acf_id_prefix = get_queried_object()->taxonomy . '_';
			}
			if ( is_author() ) {
				$acf_id_prefix = 'user_';
			}
			$relationship_values = get_field( $relationship_name, $acf_id_prefix . get_queried_object_id() );

			$relationship_ids = [];
			if ( $relationship_values && is_array( $relationship_values ) ) {
				// ACF returned object
				if ( isset( $relationship_value[0] ) && is_object( $relationship_value[0] ) && isset( $relationship_value[0]->ID ) ) {
					foreach ( $relationship_values as $relationship_value ) {
						$relationship_ids[] = $relationship_value->ID;
					}
				} else {
					// ACF returned IDs
					$relationship_ids = $relationship_values;
				}
			}


			add_filter( 'jet-engine/listings/macros-list', function ( $macros_list ) use ( $macro_name, $relationship_ids, $fallback ) {

				// avoid overwriting macros
				if ( isset( $macros_list[ $macro_name ] ) ) {
					return $macros_list;
				}

				$macros_list[ $macro_name ] = function () use ( $relationship_ids, $fallback ) {
					if ( $relationship_ids && is_array( $relationship_ids ) ) {
						$relationship_ids = implode( ",", $relationship_ids );
					}
					if ( ! $fallback && ! $relationship_ids ) {
						$relationship_ids = 0;
					}

					return $relationship_ids;
				};

				return $macros_list;
			}, 10, 1 );


		}

	}

	public function add_plugin_page() {
		add_submenu_page(
			null,
			'ACF Relations +',
			'ACF Relations +',
			'manage_options',
			'edit.php?post_type=gloo_acf_rb'
		);
	}


}


// Instantiate Plugin Class
Plugin::instance();