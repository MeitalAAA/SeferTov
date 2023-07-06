<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

class User_Post_Ids extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'user-post-ids';
	}

	/**
	 * Get Title
	 *
	 * Returns the title of the Tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Current User Post Ids', 'gloo_for_elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @return array
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_categories() {
		return [
			\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY
		];
	}

	/**
	 * Register Controls
	 *
	 * Registers the Dynamic tag controls
	 *
	 * @return void
	 * @since 2.0.0
	 * @access protected
	 *
	 */
	protected function _register_controls() {
		$posts = [];

		$args = array(
			'public'   => true,
			'_builtin' => false
		);

		$output   = 'objects';
		$operator = 'or';

		$post_types = get_post_types( $args, $output, $operator );

		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_obj ) {
				$posts[ $post_obj->name ] = $post_obj->label;
			}
		}

		$this->add_control(
			'gloo_post_type',
			array(
				'label'   => __( 'Post Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $posts,
				'default' => 'single',
			)
		);

		$this->add_control(
			'gloo_post_status',
			array(
				'label'   => __( 'Post Status', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'publish' => 'Publish',
					'draft' => 'Draft',
					'pending' => 'Pending',
					'future' => 'Future',
					'private' => 'Private',
					'trash' => 'Trash'
 				],
				'default' => 'publish',
			)
		);
	}

	public function render() {
		$post_ids  = [];
		$settings  = $this->get_settings_for_display();
		$post_type = $settings['gloo_post_type'];
		$post_status = $settings['gloo_post_status'];
 		$post_status = (!empty($post_status)) ? $post_status : array('publish');
 		
		if ( is_user_logged_in() && ! empty( $post_type ) ) {

			$current_user = wp_get_current_user();

			$args = array(
				'post_type'      => $post_type,
				'author'         => $current_user->ID,
				'orderby'        => 'post_date',
				'order'          => 'ASC',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'post_status' 	 => $post_status
			);

			$custom_query = new \WP_Query( $args );

			if ( $custom_query->posts ) {
				$post_ids = $custom_query->posts;
			}

			// Reset Query
			wp_reset_query();

			if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
				echo implode( ',', $post_ids );
			}
		}
	}
}