<?php
namespace Gloo\Modules\WooCommerce_Dynamic_Tags_Kit;

Class Product_Gallery_Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

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
		return 'woocoommerce-product-gallery-image';
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
		return __( 'Product Gallery', 'gloo_for_elementor' );
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
			'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
			)
		);

	}


	public function get_value( array $options = array() ) {
		
	 	$array_index = $this->get_settings( 'array_index' );

		global $product;
		global $post;
		
		if ( get_post_type( $post ) === 'product' && is_a($product, 'WC_Product') ) {

			$product = wc_get_product( get_the_id() );
			$attachment_ids = $product->get_gallery_image_ids();
 			
			$image_id = '';

			if(!empty($attachment_ids) && is_array($attachment_ids)) {
 
				if ( isset( $attachment_ids[ $array_index ] ) && ! empty( $attachment_ids[ $array_index ] ) ) {
					$image_id = $attachment_ids[ $array_index ];
				}

				if ( $image_id ) {
					return array(
						'id' => $image_id,
						'url' => wp_get_attachment_url($image_id),
					);
				}  

			}
 
		} else {
			return;
		}		  

	}

}