<?php
namespace Gloo\Modules\WooCommerce_Dynamic_Tags_Kit;

Class Product_Gallery_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'woocoommerce-product-gallery';
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
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
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

		if(function_exists('get_intermediate_image_sizes')) {
			$default_image_sizes = get_intermediate_image_sizes();
		} 

		$image_sizes = [];

		if(!empty($default_image_sizes)) {

			foreach($default_image_sizes as $image ) {
				$image_sizes[$image] = str_replace('_',' ',$image);
			}
		}

		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_limeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		$return_value = [
			'id' => 'ID',
			'link'  => 'Link',
			'img'  => 'Img'
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
			'return_value',
			array(
				'label'   => __( 'Return Value', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'title',
				'options' => $return_value,
				'condition' => [
					'field_output' => array('type_ul','type_ol','type_limeter','type_array')
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
			'img_size',
			array(
				'label'     => __( 'Image Size', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'title',
				'options' => $image_sizes,
				'condition' => [
					'return_value' => 'img'
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
		
		$return_value = $this->get_settings( 'return_value' );
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$img_size = $this->get_settings( 'img_size' );
		$array_index = $this->get_settings( 'array_index' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		if(empty($img_size)) {
			$img_size = 'full';
		}

		global $product;
		global $post;
		
		if ( get_post_type( $post ) === 'product' && is_a($product, 'WC_Product') ) {

			$product = wc_get_product( get_the_id() );
			$attachment_ids = $product->get_gallery_image_ids();

			if($return_value == 'id') {

				foreach($attachment_ids as $image_id) {
					$gallery_data[] = $image_id;
				}

			} elseif($return_value == 'link') {

				foreach($attachment_ids as $image_id) {
					$gallery_data[] = wp_get_attachment_url($image_id);
				}

			} else {
				foreach($attachment_ids as $image_id) {
					$gallery_data[] = wp_get_attachment_image($image_id, $img_size, false, ['class' => 'gloo-gallery-thumb']);
				}
			}

			$output = '';

			if(!empty($gallery_data) && is_array($gallery_data)) {

				if ( $field_output == 'type_ul' ) {

					$output .= '<ul class="tax-ul">';

					foreach ( $gallery_data as $value ) {
						$output .= '<li>' . $value . '</li>';
					}

					$output .= '</ul>';

				} else if ( $field_output == 'type_ol' ) {

					$output .= '<ol class="tax-ol">';

					foreach ( $gallery_data as $value ) {
						$output .= '<li>' . $value . '</li>';
					}

					$output .= '</ol>';


				} else if ( $field_output == 'type_lenght' ) {

					$output = count( $gallery_data );

				} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

					$output = implode( $delimiter, $gallery_data );

				} else if ( $field_output == 'type_array' && ! empty( $array_index ) ) {

					if ( isset( $gallery_data[ $array_index ] ) && ! empty( $gallery_data[ $array_index ] ) ) {
						$output = $gallery_data[ $array_index ];
					}

				}
				else if ( $field_output == 'one_per_line' ) {
					if($one_per_line_type == 'html')
						$output = implode( '<br />', $gallery_data );
					else
						$output = implode( PHP_EOL, $gallery_data );

				}

				echo $output;

			}

		} else {
			return;
		}
 
		  

	}

}