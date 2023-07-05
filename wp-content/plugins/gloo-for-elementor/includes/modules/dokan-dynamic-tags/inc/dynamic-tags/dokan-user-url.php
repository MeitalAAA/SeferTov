<?php
namespace Gloo\Modules\Dokan_Dynamic_Tags;

Class Dokan_User_Url_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'dokan-url-dynamic-tag';
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
		return __( 'Store User Url', 'gloo_for_elementor' );
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

	protected function _register_controls() {
 
        $this->add_control(
			'gloo_store_name_note',
			[
                'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Note: it will return Dokan user url', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
        );

	}

	public function render() {
	
		global $product;
		if ( is_a( $product, 'WC_Product' ) ) {

			$seller = get_post_field( 'post_author', $product->get_id());
			$author     = get_user_by( 'id', $seller );

			$store_url = dokan_get_store_url( $author->ID );
			if ( !empty( $store_url ) ) {  
				echo $store_url;
			}
		}
	}

}