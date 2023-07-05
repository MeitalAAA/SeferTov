<?php

namespace Gloo\Modules\Dynamify_Repeaters;

class Dynamify_Tag_WC_Variations extends Dynamify_Tag {

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
		return 'gloo-dynamify-tag-wc-variations';
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
		return __( 'Dynamify Tag WC Variations', 'gloo_for_elementor' );
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
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::NUMBER_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::COLOR_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
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
	protected function register_controls() {

		$wc_fields = apply_filters( 'gloo/modules/dynamify_repeaters/wc_variation_fields', [
			'dimensions' => [
				'label'   => __( 'Dimensions', 'gloo_for_elementor' ),
				'options' => [
					'length' => 'Length',
					'width'  => 'Width',
					'height' => 'Height',
				],
			],
			'price'      => [
				'label'   => __( 'Price', 'gloo_for_elementor' ),
				'options' => [
					'display_price'         => 'Sale Price',
					'display_regular_price' => 'Regular Price',
					'price_html'            => 'Price HTML',
				],
			],
			'quantity'   => [
				'label'   => __( 'Quantity', 'gloo_for_elementor' ),
				'options' => [
					'max_qty' => 'Max Quantity',
					'min_qty' => 'Min Quantity',
					'_stock'  => 'Stock',
				],
			],
			'other'      => [
				'label'   => __( 'Other', 'gloo_for_elementor' ),
				'options' => [
					'variation_description' => 'Description',
					'sku'                   => 'SKU',
					'weight'                => 'Weight',
					'image_id'              => 'Image ID',
					'image'                 => 'Image',
					'variation_id'          => 'Variation ID',
					'availability_html'     => 'Availability HTML',
					'dimensions_html'       => 'Dimensions HTML',
					'weight_html'           => 'Weight HTML',
				],
			],

			'custom' => [
				'label'   => __( 'Custom', 'gloo_for_elementor' ),
				'options' => [
					'custom' => 'Custom Field',
				],
			],
		] );

		$this->add_control(
			'wc_field',
			array(
				'label'       => __( 'WooCommerce Variation Field', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::SELECT,
				'groups'      => $wc_fields

			)
		);

		$this->add_control(
			'image_output',
			array(
				'label'       => __( 'Image Output', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					'url'           => 'URL',
					'title'         => 'Title',
					'image_field' => 'For Image Field',
				],
				'condition'   => [
					'wc_field' => 'image'
				]
			)
		);


		$this->add_control(
			'custom_wc_field',
			array(
				'label'       => __( 'Custom Field', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => 'Example:<br>
				<b>_stock</b> for product stock.<br>
				See more examples <a href="https://wwww.gloo.ooo/" target="_blank">here</a>.',
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'wc_field' => 'custom',
				]

			)
		);


		$this->add_control(
			'index',
			array(
				'label'       => __( 'Array Index', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => '0',
				'classes'     => 'gloo-hidden-control',
				'dynamic'     => [
					'active' => true,
				],

			)
		);
	}


	public function get_value( array $options = array() ) {

		$product_id = $this->get_settings_for_display( 'field' );
		$index      = intval( $this->get_settings_for_display( 'index' ) );

		$wc_field = $this->get_settings_for_display( 'wc_field' );

		// check if its a custom field
		if ( $wc_field === 'custom' ) {
			$wc_field = $this->get_settings_for_display( 'custom_wc_field' );
		}

		return $this->wc_variations_get_data( $product_id, $index, $wc_field );
	}

	public function wc_variations_get_data( $product_id, $index, $wc_field ) {

		$product = wc_get_product( $product_id );

		// bail early
		if ( ! $product || ! $wc_field ) {
			return;
		}

		$variations = $product->get_available_variations();

		if ( ! $variations ) { // no variations
			return;
		}

		if ( ! isset( $variations[ $index ] ) ) {
			return;
		}

		// image output
		if ( $wc_field === 'image' ) {
			return $this->get_image_field( $this->get_settings_for_display( 'image_output' ), $variations[ $index ][ $wc_field ] );
		}

		// custom field
		if ( ! isset( $variations[ $index ][ $wc_field ] ) && isset( $variations[ $index ]['variation_id'] ) ) {
			return get_post_meta( $variations[ $index ]['variation_id'], $wc_field, true );
		}

		return isset( $variations[ $index ][ $wc_field ] ) ? $variations[ $index ][ $wc_field ] : null;
	}

	public function get_image_field( $format, $image_array ) {

		if ( ! isset( $image_array['url'] ) ) {
			return;
		}

		switch ( $format ) {
			case 'image_field' :
				$output = [
					'id'  => attachment_url_to_postid( $image_array['url'] ),
					'url' => $image_array['url'],
				];
				break;
			case 'title' :
				$output = 'title';
				break;
			default :
				$output = $image_array['url'];
		}

		return $output;
	}


}