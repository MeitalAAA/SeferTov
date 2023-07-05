<?php

namespace Gloo\Modules\Dynamic_Nav;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Plugin {

	/**
	 * Plugin Version
	 *
	 * @var string The plugin version.
	 */
	const DNM_EL_ADDON_VERSION = '1.0.3';

	private static $menu_index = 1;
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

		// register term
		add_action( 'admin_init', [ $this, 'register_term_meta_dnm' ] );

		// add meta box to posts
		add_action( 'add_meta_boxes', [ $this, 'dnm_add_meta_boxes' ] );

		add_action( 'save_post', [ $this, 'dnm_save_post' ] );

		// Elementor
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'dnm_maybe_change_nav_menu' ] );

		add_action( 'elementor/element/nav-menu/section_layout/before_section_end', [
			$this,
			'dnm_add_elementor_control'
		], 10, 2 );

		add_action('admin_enqueue_scripts', [$this, 'gloo_enqueue_admin_script']);
		require gloo()->modules_path( 'dynamic-nav/inc/PageSettings.php' );
		$PageSettings = new \Gloo\Modules\Dynamic_Nav\PageSettings();

	}

	public function dnm_get_taxonomy_list() {
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		if ( ! $taxonomies ) {
			return;
		}
		$list = [];
		foreach ( $taxonomies as $taxonomy_slug => $taxonomy_object ) {
			$list[ $taxonomy_slug ] = $taxonomy_object->label;
		}

		return $list;
	}

	public function dnm_save_post() {
		global $post;

		// auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST["dnm_elementor_choice1"] ) ) {
			$value = $_POST["dnm_elementor_choice1"];
			update_post_meta( $post->ID, 'dnm_elementor_choice1', $value );
		}

		if ( isset( $_POST["dnm_elementor_choice2"] ) ) {
			$value = $_POST["dnm_elementor_choice2"];
			update_post_meta( $post->ID, 'dnm_elementor_choice2', $value );
		}

		$existing_items = SerializeStringToArray(get_option('gloo_dnm_elementor_addon_repeater_items'));
    if($existing_items && is_array($existing_items) && count($existing_items) >= 1){
			foreach($existing_items as $key=>$item){
				if ( isset( $_POST[$item['meta_key']."1"])){
					if($_POST[$item['meta_key']."1"]) 
						$value1 = $_POST[$item['meta_key']."1"];
					else
						$value1 = '';
					update_post_meta( $post->ID, $item['meta_key']."1", $value1 );
				}
				/*if ( isset( $_POST[$item['meta_key']."2"] ) && $_POST[$item['meta_key']."2"]) {
					$value2 = $_POST[$item['meta_key']."2"];
					update_post_meta( $post->ID, $item['meta_key']."2", $value2 );
				}*/
			}
		}

	}

	public function dnm_add_meta_boxes() {

		global $post;

		if ( empty( $post ) ) {
			return;
		}

		add_meta_box(
			'dnm_elementor_choice', // $id
			'Gloo Dynamic Nav Menu', // $title
			[ $this, 'dnm_show_meta_boxes' ], // $callback
			get_post_type( $post->ID ),
			'side' // $context
		);

	}

	public function dnm_show_meta_boxes() {

		$existing_items = SerializeStringToArray(get_option('gloo_dnm_elementor_addon_repeater_items'));
    if($existing_items && is_array($existing_items) && count($existing_items) >= 1){
			foreach($existing_items as $key=>$item){
				echo "<p>".$item['label']." Menu:</p>";
				$this->print_nav_menu_select( get_the_ID(), 'post', 1, $item['meta_key'] );
				//echo "<p>".$item['label']." Desktop menu:</p>";
				//$this->print_nav_menu_select( get_the_ID(), 'post', 2, $item['meta_key'] );
				echo '<hr />';
			}
		}else{
			echo "<p>Mobile menu:</p>";
			$this->print_nav_menu_select( get_the_ID(), 'post' );
			echo "<p>Desktop menu:</p>";
			$this->print_nav_menu_select( get_the_ID(), 'post', 2 );
		}
		
	}


	public function dnm_add_elementor_control( $element, $args ) {
		$element->start_injection( [
			'at' => 'after',
			'of' => 'menu',
		] );

		$element->add_control(
			'dnm_use_dynamic_menu',
			[
				'label'        => __( 'Use Dynamic Menu', 'elementor-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => 'Allows you to choose a unique menu for each page, falls back to the default menu.',
				'return_value' => 'yes',
			]
		);

		$existing_items = SerializeStringToArray(get_option('gloo_dnm_elementor_addon_repeater_items'));
    $existing_metaboxes = array('' => '--Select--');
		if($existing_items && is_array($existing_items) && count($existing_items) >= 1){
			foreach($existing_items as $item){
				$existing_metaboxes[$item['meta_key']] = $item['label'];
			}
		}

		$element->add_control(
			'dnm_nav_menu_from_metabox',
			[
				'label'        => __( 'Source Field ID', 'elementor-pro' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $existing_metaboxes,
				'condition'    => [
					'dnm_use_dynamic_menu' => 'yes'
				],
			]
		);

		$element->add_control(
			'dnm_use_child_page_menu_fallback',
			[
				'label'        => __( 'Use Fallback for Child Pages', 'elementor-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => 'Will fallback to parent page if nothing is selected on child page.',
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'    => [
					'dnm_use_dynamic_menu' => 'yes'
				],
			]
		);

		$element->add_control(
			'dnm_use_taxonomy_fallback',
			[
				'label'        => __( 'Use Taxonomy Fallback', 'elementor-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => 'Fallback to the menu selected in the taxonomy term, will fallback to parent taxonomy if nothing is selected.',
				'return_value' => 'yes',
				'condition'    => [
					'dnm_use_dynamic_menu' => 'yes'
				],
			]
		);

		$taxonomies = $this->dnm_get_taxonomy_list();
		if ( $taxonomies ) {
			$element->add_control(
				'dnm_taxonomy_fallback',
				[
					'label'     => __( 'Fallback Taxonomy', 'plugin-domain' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => $taxonomies,
					'condition' => [
						'dnm_use_taxonomy_fallback' => 'yes',
						'dnm_use_dynamic_menu'      => 'yes'
					],
				]
			);
		}

		$element->add_control(
			'dnm_dynamic_menu_hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$element->end_injection();
	}

	public function dnm_maybe_change_nav_menu( $widget ) {

		// check if its the nav menu widget
		if ( $widget->get_name() !== 'nav-menu' ) {
			return;
		}

		// check if dynamic menu is enabled
		$settings = $widget->get_settings();
		if ( ! isset( $settings['dnm_use_dynamic_menu'] ) || ! $settings['dnm_use_dynamic_menu'] ) {
			return;
		}
		$meta_key = 'dnm_elementor_choice';
		if(isset( $settings['dnm_nav_menu_from_metabox']) && $settings['dnm_nav_menu_from_metabox']){
			$existing_items = SerializeStringToArray(get_option('gloo_dnm_elementor_addon_repeater_items'));
			if($existing_items && is_array($existing_items) && count($existing_items) >= 1 && array_key_exists($settings['dnm_nav_menu_from_metabox'], $existing_items)){
				$meta_key = $existing_items[$settings['dnm_nav_menu_from_metabox']]['meta_key'];
			}
			
		}
		$child_pages_fallback = isset( $settings['dnm_use_child_page_menu_fallback'] ) && $settings['dnm_use_child_page_menu_fallback'] ? true : false;
		$taxonomy_fallback = isset( $settings['dnm_taxonomy_fallback'] ) && isset( $settings['dnm_use_taxonomy_fallback'] ) && $settings['dnm_taxonomy_fallback'] && $settings['dnm_use_taxonomy_fallback'] ? $settings['dnm_taxonomy_fallback'] : false;
		// change the menu
		if ( $menu_slug = $this->get_dnm_menu_slug( 1/*self::$menu_index*/, $taxonomy_fallback, $meta_key, $child_pages_fallback ) ) {
			$widget->set_settings( 'menu', $menu_slug );
		}

		self::$menu_index ++;
	}

	public function get_dnm_menu_slug( $i = 1, $taxonomy_fallback = false, $meta_key = 'dnm_elementor_choice', $child_pages_fallback = false ) {

		if ( is_tax() || is_category() || is_tag() ) { // taxonomy
			$result = $this->get_term_meta_dnm( get_queried_object_id(), $i, $meta_key );
			if ( ! $taxonomy_fallback ) {
				return $result;
			}
			if ( ( ! $result && taxonomy_exists( $taxonomy_fallback ) || ! is_nav_menu( $result ) ) && get_queried_object()->parent != 0 ) {

				$ancestors = get_ancestors( get_queried_object_id(), $taxonomy_fallback );
				if ( ! $ancestors ) {
					return $result;
				}
				foreach ( $ancestors as $ancestor ) {
					if ( is_nav_menu( $this->get_term_meta_dnm( $ancestor, $i, $meta_key ) ) ) {
						$result = $this->get_term_meta_dnm( $ancestor, $i, $meta_key );
						break;
					}
				}
			}

			return $result;
		} else { // posts

			$gloo_current_object_id = get_the_ID();
			if(is_home()){
				$page_for_posts = get_option( 'page_for_posts' );
				if($page_for_posts)
					$gloo_current_object_id = $page_for_posts;
			}

			$result = $this->get_post_meta_dnm( $gloo_current_object_id, $i, $meta_key );

			if(is_singular('page') && $child_pages_fallback){
				global $post;
				if($post->post_parent && !$result){
					$result = $this->get_post_meta_dnm( $post->post_parent, $i, $meta_key );
					if($result)
						return $result;
				}
			}

			if ( ( ! $result || ! is_nav_menu( $result ) ) && $taxonomy_fallback ) {
				if ( ! taxonomy_exists( $taxonomy_fallback ) ) {
					return $result;
				}
				$terms = get_the_terms( get_the_ID(), $taxonomy_fallback );
				if ( ! $terms || is_wp_error( $terms ) ) {
					return $result;
				}

				if ( $result = $this->get_term_meta_dnm( $terms[0]->term_id, $i, $meta_key ) ) {
					return $result;
				}

				$ancestors = get_ancestors( $terms[0]->term_id, $taxonomy_fallback );
				if ( ! $ancestors ) {
					return $result;
				}

				foreach ( $ancestors as $ancestor ) {
					if ( is_nav_menu( $this->get_term_meta_dnm( $ancestor, $i, $meta_key ) ) ) {
						$result = $this->get_term_meta_dnm( $ancestor, $i, $meta_key );
						break;
					}
				}
			}

			return $result;
		}
	}

	public function save_term_meta_dnm( $term_id ) {

		$old_value = $this->get_term_meta_dnm( $term_id, 1 );
		$new_value = isset( $_POST['dnm_elementor_choice1'] ) ? $this->sanitize_meta_dnm( $_POST['dnm_elementor_choice1'] ) : '';


		if ( $old_value && '' === $new_value ) {
			delete_term_meta( $term_id, 'dnm_elementor_choice1' );
		} else {
			update_term_meta( $term_id, 'dnm_elementor_choice1', $new_value );
		}


		$old_value = $this->get_term_meta_dnm( $term_id, 2 );
		$new_value = isset( $_POST['dnm_elementor_choice2'] ) ? $this->sanitize_meta_dnm( $_POST['dnm_elementor_choice2'] ) : '';

		if ( $old_value && '' === $new_value ) {
			delete_term_meta( $term_id, 'dnm_elementor_choice2' );
		} else {
			update_term_meta( $term_id, 'dnm_elementor_choice2', $new_value );
		}



		$existing_items = SerializeStringToArray(get_option('gloo_dnm_elementor_addon_repeater_items'));
    if($existing_items && is_array($existing_items) && count($existing_items) >= 1){
			foreach($existing_items as $key=>$item){
				
				$old_value = $this->get_term_meta_dnm( $term_id, 1, $item['meta_key'] );
				$new_value = isset( $_POST[$item['meta_key'].'1'] ) ? $this->sanitize_meta_dnm( $_POST[$item['meta_key'].'1'] ) : '';
				if ( $old_value && '' === $new_value ) 
					delete_term_meta( $term_id, $item['meta_key'].'1' );
				else
					update_term_meta( $term_id, $item['meta_key'].'1', $new_value );
				

					/*$old_value = $this->get_term_meta_dnm( $term_id, 2, $item['meta_key'] );
					$new_value = isset( $_POST[$item['meta_key'].'2'] ) ? $this->sanitize_meta_dnm( $_POST[$item['meta_key'].'2'] ) : '';
					if ( $old_value && '' === $new_value )
						delete_term_meta( $term_id, $item['meta_key'].'2' );
					else
						update_term_meta( $term_id, $item['meta_key'].'2', $new_value );*/				

			}
		}

	}

	public function add_form_field_term_meta_dnm() {
		?>
        <div class="form-field term-meta-text-wrap">
            <label for="term-meta-text"><?php _e( 'Gloo Dynamic Menu', 'dnm_elementor_addon' ); ?></label>
			<?php
			$existing_items = SerializeStringToArray(get_option('gloo_dnm_elementor_addon_repeater_items'));
			if($existing_items && is_array($existing_items) && count($existing_items) >= 1){
				foreach($existing_items as $key=>$item){
					echo "<p>".$item['label']." Menu:</p>";
					$this->print_nav_menu_select( '', 'term', 1, $item['meta_key'] );
					//echo "<p>".$item['label']." Desktop menu:</p>";
					//$this->print_nav_menu_select( '', 'term', 2, $item['meta_key'] );
					echo '<hr />';
				}
			}else{
				echo "<p>Mobile menu:</p>";
				$this->print_nav_menu_select();
				echo "<p>Desktop menu:</p>";
				$this->print_nav_menu_select( '', 'term', 2 );
			}

			
			?>
            <p>The <b>Dynamic Menu</b> option has to be enabled on the Elementor <b>Nav Menu</b> Widget.</p>
        </div>
		<?php
	}

	public function print_nav_menu_select( $id = '', $context = 'term', $i = 1, $meta_key = 'dnm_elementor_choice' ) {
		if ( $context === 'term' ) {
			$value = $this->get_term_meta_dnm( $id, $i, $meta_key );
		}

		if ( $context === 'post' ) {
			$value = $this->get_post_meta_dnm( $id, $i, $meta_key );
		}

		$menus   = wp_get_nav_menus();
		$options = '';
		foreach ( $menus as $key => $menu ) {
			$is_selected = isset( $value ) && $value == $menu->slug ? 'selected' : '';
			$options     .= "<option value='$menu->slug' $is_selected>$menu->name</option>";
		}
		if ( $options ) {
			echo "<select name='{$meta_key}{$i}' id='term-meta-text'><option value=''>" . __( 'Disabled', 'dnm-elementor-addon' ) . "</option>$options</select>";
		}
	}

	public function edit_form_field_term_meta_dnm( $term ) {

		?>
        <tr class="form-field term-meta-text-wrap">
            <th scope="row"><label
                        for="term-meta-text"><?php _e( 'Gloo Dynamic Menu', 'dnm_elementor_addon' ); ?></label></th>
            <td>
				<?php

					$existing_items = SerializeStringToArray(get_option('gloo_dnm_elementor_addon_repeater_items'));
					if($existing_items && is_array($existing_items) && count($existing_items) >= 1){
						foreach($existing_items as $key=>$item){
							echo "<p>".$item['label']." Menu:</p>";
							$this->print_nav_menu_select( $term->term_id, 'term', 1, $item['meta_key'] );
							//echo "<p>".$item['label']." Desktop menu:</p>";
							//$this->print_nav_menu_select( $term->term_id, 'term', 2, $item['meta_key'] );
							echo '<hr />';
						}
					}else{
						echo "<p>Mobile menu:</p>";
						$this->print_nav_menu_select( $term->term_id );
						echo "<p>Desktop menu:</p>";
						$this->print_nav_menu_select( $term->term_id, 'term', 2 );
					}

				
				?>
            </td>
        </tr>
		<?php
	}


	public function register_term_meta_dnm() {
		register_meta( 'term', 'dnm_elementor_choice1', 'sanitize_meta_dnm' );
		register_meta( 'term', 'dnm_elementor_choice2', 'sanitize_meta_dnm' );

		$taxonomies = get_taxonomies();


		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				add_action( "{$taxonomy}_add_form_fields", [ $this, 'add_form_field_term_meta_dnm' ] );
				add_action( "{$taxonomy}_edit_form_fields", [ $this, 'edit_form_field_term_meta_dnm' ] );
				add_action( "edit_{$taxonomy}", [ $this, 'save_term_meta_dnm' ] );
				add_action( "create_{$taxonomy}", [ $this, 'save_term_meta_dnm' ] );
			}
		}


	}

	public function sanitize_meta_dnm( $value ) {
		return sanitize_title( $value );
	}

	// Getters

	public function get_term_meta_dnm( $term_id, $i, $meta_key =  'dnm_elementor_choice') {
		$value = get_term_meta( $term_id,  $meta_key. $i, true );
		$value = $this->sanitize_meta_dnm( $value );

		return $value;
	}

	public function get_post_meta_dnm( $post_id, $i, $meta_key =  'dnm_elementor_choice' ) {
		$value = get_post_meta( $post_id, $meta_key . $i, true );
		$value = $this->sanitize_meta_dnm( $value );

		return $value;
	}


	public function gloo_enqueue_admin_script($hook)
	{
		
		wp_enqueue_style('bbwp_repeater', gloo()->plugin_url( 'includes/modules/dynamic-nav/assets/css/admin/bbwp_repeater.css') , null, '1.0');
		wp_enqueue_script('bbwp_repeater', gloo()->plugin_url( 'includes/modules/dynamic-nav/assets/js/admin/bbwp_repeater.js'), ['jquery'], '1.0');

		/*wp_localize_script(
			'gloo',
			'glooData',
			array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
			)
		);*/
	}

}

// Instantiate Plugin Class
Plugin::instance();
