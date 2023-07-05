<?php
namespace Gloo\Modules\WooCommerce_Dynamic_Tags_Kit;

Class Product_Attribute_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'woocoommerce-tags';
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
		return __( 'Product Attribute Value', 'gloo_for_elementor' );
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

		$attributes_array = [];

		if(function_exists('wc_get_attribute_taxonomies')) {

			$attributes = wc_get_attribute_taxonomies();

			if(!empty($attributes)) {
				foreach ($attributes as $attribute) {
					$attributes_array[$attribute->attribute_name] = ucwords($attribute->attribute_label);
				}
			}
		}

		$this->add_control(
			'field_type',
			[
				'label'        => __( 'Show Label/Value', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Value', 'gloo_for_elementor' ),
				'label_off'    => __( 'Label', 'gloo_for_elementor' ),
				'return_value' => 'value',
				'default'      => 'label',
			]
		);

		$this->add_control(
			'choose_attribute',
			array(
				'label'   => __( 'Select Attribute', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $attributes_array,
			)
		);

		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_limeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		$this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_none',
				'options' => $output_option,
			)
		);

		$this->add_control(
			'one_per_line_type',
			array(
				'label'     => __( 'Line Break Type', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default' => 'php',
				'options' => array('php' => 'PhP', 'html' => 'HTML'),
				'condition' => [
					'field_output' => 'one_per_line'
				],
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'field_output' => 'type_limeter'
				],
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'condition' => [
					'field_output' => 'type_array'
				],
			)
		);

	}


	public function render() {

		$field_type = $this->get_settings( 'field_type' );
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$array_index = $this->get_settings( 'array_index' );
		$choosen_attribute = $this->get_settings( 'choose_attribute' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		global $product;

		if ( get_post_type( $post ) === 'product' && ! is_a($product, 'WC_Product') ) {

			$product = wc_get_product( get_the_id() );

		}

		if(!empty($choosen_attribute)) {

			if(!empty($product)) {

				$attribute_label = [];
				$attribute_value = [];

				$attributes = wc_get_product_terms( $product->id, 'pa_'.$choosen_attribute );

				if( ! empty( $attributes ) && ! is_wp_error( $attributes ) ) {

					foreach ($attributes as $attribute_val) {
						$array_value[] = ucwords($attribute_val->name);
					}

				} else {

					return;
				}

				$array_name[] = wc_attribute_label( $choosen_attribute );

				$output = '';

				if($field_type == 'value') {
					$data = $array_value;
				} else {
					$data = $array_name;
				}

				if ( is_array( $data ) ) {

					if ( $field_output == 'type_ul' ) {

						$output .= '<ul class="attribute-ul">';

						foreach ( $data as $value ) {
							$output .= '<li>' . $value . '</li>';
						}

						$output .= '</ul>';

					} else if ( $field_output == 'type_ol' ) {

						$output .= '<ol class="attribute-ol">';

						foreach ( $data as $value ) {
							$output .= '<li>' . $value . '</li>';
						}

						$output .= '</ol>';

					} else if ( $field_output == 'type_lenght' ) {

						$output = count( $data );

					} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

						$output = implode( $delimiter, $data );

					} else if ( $field_output == 'type_array' && ! empty( $array_index ) ) {

						if ( isset( $data[ $array_index ] ) && ! empty( $data[ $array_index ] ) ) {
							$output = $data[ $array_index ];
						}

					}
					else if ( $field_output == 'one_per_line' ) {
						if($one_per_line_type == 'html')
							$output = implode( '<br />', $data );
						else
							$output = implode( PHP_EOL, $data );
	
					}

				}

				echo $output;
			}
		}
	}

}