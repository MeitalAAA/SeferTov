<?php
namespace Gloo\Modules\Facebook_Reviews;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Facebook_Review {

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

		// Register Custom Post Type
		add_action( 'init', [ $this, 'gloo_dynamic_tag_post_type' ], 0 );

		// add meta box to posts
		add_action( 'add_meta_boxes', [ $this, 'fb_add_meta_boxes' ] );
		add_action('admin_enqueue_scripts', [ $this, 'fb_meta_box_scripts']);

		// // save
		add_action( 'save_post_gloo_fb', [ $this, 'fb_save_post' ] );

	}

	public function gloo_dynamic_tag_post_type() {

		$labels = array(
			'name'           => _x( 'Facebook Review', 'Post Type General Name', 'gloo_for_elementor' ),
			'singular_name'  => _x( 'Facebook Review', 'Post Type Singular Name', 'gloo_for_elementor' ),
			'menu_name'      => __( 'Facebook Review', 'gloo_for_elementor' ),
			'name_admin_bar' => __( 'Facebook Review', 'gloo_for_elementor' ),
			'add_new'	=> __( 'Add Facebook Review', 'gloo_for_elementor' ),
			'set_featured_image'	=> __( 'Set User Photo', 'gloo_for_elementor' ),
			'remove_featured_image'	=> __( 'Remove User Photo', 'gloo_for_elementor' ),
			'featured_image'	=> __( 'User Photo', 'gloo_for_elementor' ),
			'edit_item'	=> __( 'Edit Facebook Review', 'gloo_for_elementor' )
		);
		$args   = array(
			'label'               => __( 'Facebook Review', 'gloo_for_elementor' ),
			'description'         => __( 'Facebook Review', 'gloo_for_elementor' ),
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-star-filled',
			'supports'            => array( 'title','thumbnail' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);
		register_post_type( 'gloo_fb', $args );

	}

	public function fb_add_meta_boxes() {

		global $post;

		if ( empty( $post ) ) {
			return;
		}

		add_meta_box(
			'fb_datasource_choice', // $id
			'Google Review Option', // $title
			[ $this, 'fb_show_meta_boxes' ], // $callback
			'gloo_fb',
			'normal' // $context
		);

	}

	public function fb_meta_box_scripts() {
		$screen = get_current_screen();

		if (is_object($screen)) {
			// enqueue only for specific post types
			if (in_array($screen->post_type, ['gloo_fb'])) {
				// enqueue script
				
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
				wp_enqueue_script('gloo_fb_meta_box_script', gloo()->plugin_url( 'assets/js/admin/gloo-review.js' ), [ 'jquery' ], gloo()->get_version() );
				
			}
		}

	}

	public function fb_show_meta_boxes($data) { ?>
	
		<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table gloo-fb-metabox">
			<tbody>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label><?php _e('Rating', 'gloo_for_elementor'); ?></label>
				</th>
				<td>
					<?php $fb_rating =  get_post_meta( $data->ID, 'fb_rating', true ); ?>
					<input name="fb_rating" type="text" style="width: 95%" value="<?php echo $fb_rating;?>">
				</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label><?php _e('Date', 'gloo_for_elementor'); ?></label>
				</th>
				<td>
					<?php $fb_date =  get_post_meta( $data->ID, 'fb_date', true ); ?>
					<input name="fb_date" id="js-comment-date" type="text" style="width: 95%" value="<?php echo $fb_date;?>">
				</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label><?php _e('User Comment', 'gloo_for_elementor'); ?></label>
				</th>
				<td>
					<?php $fb_user_comment =  get_post_meta( $data->ID, 'fb_user_comment', true ); ?>
					<textarea name="fb_user_comment" style="width: 95%" cols="50" rows="5" ><?php echo $fb_user_comment;?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}


	public function fb_save_post() {
		global $post;
		$user_id = get_current_user_id();
		
		// auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if ( isset( $_POST["fb_rating"] ) ) {
			update_post_meta( $post->ID, 'fb_rating', $_POST["fb_rating"] );
		}

		if ( isset( $_POST["fb_date"] )) {
			update_post_meta( $post->ID, 'fb_date', $_POST["fb_date"] );
		}

		if(isset($_POST['fb_user_comment'])) {
			update_post_meta( $post->ID, 'fb_user_comment', $_POST['fb_user_comment'] );
		} 		
		
	}

}


// Instantiate Plugin Class
Facebook_Review::instance();