<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

Class User_Avtar_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'user-avtar-image';
	}

	/**
	 * Get Title
	 *
	 * Returns the title of the Tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'User Avtar', 'gloo_for_elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY ];
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

		$this->add_control(
			'user_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => array(
					'current_user' => __( 'Current User', 'gloo_for_elementor' ),
					'queried_user' => __( 'Queried User', 'gloo_for_elementor' ),
					'current_post_author' => __( 'Current Post Author', 'gloo_for_elementor' )
				),
			)
		);

		$this->add_control(
			'user_avtar_width',
			array(
				'label'       => __( 'Avtar Width', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default' => 96,
				'dynamic'     => [
					'active' => true,
				],
			)
		);

	}


	public function get_value( array $options = array() ) {
		$context = $this->get_settings_for_display( 'user_context' );
		$user_avtar_width = $this->get_settings_for_display( 'user_avtar_width' );

		if ( ! $context ) {
			$context = 'current_user';
		}

		if ( 'current_user' === $context ) {
			
			$user_object = wp_get_current_user();

		} elseif('current_post_author' === $context ) {
			
			$post = get_post( get_the_ID() );

			if(isset($post->post_author)) {
				$user = get_user_by('ID', $post->post_author);
				//$user_object = $user->data;
				$user_object = $user;
			}
			
		} else {
			$user_object = jet_engine()->listings->data->get_current_user_object();
		}

		if(!empty($user_object)) {
			$args = [
				'size' => $user_avtar_width
			];
			
			$url = get_avatar_url($user_object->data->ID, $args);
			
			return [
				'url' => $url
			];
		}
	}
}