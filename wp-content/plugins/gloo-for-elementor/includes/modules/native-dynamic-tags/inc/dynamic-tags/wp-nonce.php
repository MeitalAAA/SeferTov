<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

Class WP_Nonce extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-wp-nounce-tag';
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
		return __( 'WP Nonce Tag', 'gloo_for_elementor' );
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

		$this->add_control(
			'logout_url',
			[
				'label' => __( 'Logout Url', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo_for_elementor' ),
				'label_off' => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'nounce_url',
			[
				'label' => __( 'Link', 'gloo_for_elementor' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-link.com', 'gloo_for_elementor' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'logout_url',
							'operator' => '!=',
							'value' => 'yes'
						]
					]
				]
			]
		);

	}

	public function render() {

		$url = $this->get_settings( 'nounce_url' );
		$logout_url = $this->get_settings( 'logout_url' );

		if( !empty( $logout_url ) && $logout_url == 'yes' ) {
			echo wp_logout_url();
		} else {
			
			if( !empty( $url ) ) {

				if( function_exists( 'add_query_arg' ) ) {
					echo add_query_arg( array(
						'_wpnonce' => wp_create_nonce(),
					), $url );
				}	
			}	 
		}

	}

}