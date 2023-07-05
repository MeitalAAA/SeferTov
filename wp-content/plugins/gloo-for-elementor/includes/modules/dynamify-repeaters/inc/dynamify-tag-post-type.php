<?php

namespace Gloo\Modules\Dynamify_Repeaters;

class Dynamify_Tag_Post_Type extends Dynamify_Tag {

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
		return 'gloo-dynamify-tag-post-type';
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
		return __( 'Dynamify Tag Post Type', 'gloo_for_elementor' );
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

		$post_fields = apply_filters( 'gloo/modules/dynamify_repeaters/post_type_fields', [
			'post' => [
				'label'   => __( 'Post', 'gloo_for_elementor' ),
				'options' => [
					'id' => 'ID',
					'title'  => 'Title',
					'content' => 'Content',
				],
			],
			// 'other'      => [
			// 	'label'   => __( 'Other', 'gloo_for_elementor' ),
			// 	'options' => [
			// 		'variation_description' => 'Description',
			// 		'sku'                   => 'SKU',
			// 		'weight'                => 'Weight',
			// 		'image_id'              => 'Image ID',
			// 		'image'                 => 'Image',
			// 		'variation_id'          => 'Variation ID',
			// 		'availability_html'     => 'Availability HTML',
			// 		'dimensions_html'       => 'Dimensions HTML',
			// 		'weight_html'           => 'Weight HTML',
			// 	],
			// ],
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
				'groups'      => $post_fields
			)
		);

		$this->add_control(
			'custom_wc_field',
			array(
				'label'       => __( 'Custom Field', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => 'Example:<br>
				<b>content</b> for post content.<br>
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

		// $product_id = $this->get_settings_for_display( 'field' );
		$index      = intval( $this->get_settings_for_display( 'index' ) );

		$wc_field = $this->get_settings_for_display( 'wc_field' );

		// check if its a custom field
		if ( $wc_field === 'custom' ) {
			$wc_field = $this->get_settings_for_display( 'custom_wc_field' );
		}

		return $this->post_type_get_data( $index, $wc_field );
	}

	public function post_type_get_data( $index, $wc_field ) {

    $post = get_post( $post_id, OBJECT );


		// bail early
		if ( ! $post || ! $wc_field ) {
			return;
		}

    $query_args = [
      'posts_per_page' => - 1,
      'fields'         => 'ids',
    ];

    return $index;


		// // custom field
		// if ( ! isset( $variations[ $index ][ $wc_field ] ) && isset( $variations[ $index ]['variation_id'] ) ) {
		// 	return get_post_meta( $variations[ $index ]['variation_id'], $wc_field, true );
		// }

		// return isset( $variations[ $index ][ $wc_field ] ) ? $variations[ $index ][ $wc_field ] : null;
	}


}