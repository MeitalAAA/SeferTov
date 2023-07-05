<?php

namespace Gloo\Modules\Product_Related_Courses_Tag;

Class Product_Related_Courses extends \Elementor\Core\DynamicTags\Tag {

	private $prefix = 'gloo_';
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
		return 'gloo-product-related-courses';
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
		return __( 'Product\'s Related Courses', 'gloo_for_elementor' );
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

		$products = [];

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
		);
	
		$products_lists = get_posts($args);

		if(!empty($products_lists)) {
			foreach($products_lists as $single_product) {
				$products[$single_product->ID] = $single_product->post_title;
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
			'title'      => 'Title',
			'slug'      => 'Slug',
 			'id' => 'ID',
			'link'  => 'Link'
		];

		$this->add_control(
			$this->prefix . 'current_product',
			[
				'label'        => __( 'Current Product', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			$this->prefix.'product_id',
			array(
				'label'     => __( 'Product Id', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'current_product',
							'operator' => '!=',
							'value' => 'yes'
						],
					]
				]
			)
		);
		
		$this->add_control(
			$this->prefix.'return_value',
			array(
				'label'   => __( 'Return Value', 'gloo_for_elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'id',
				'options' => $return_value,
			)
		);

		$this->add_control(
			$this->prefix.'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
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
					$this->prefix.'field_output' => 'one_per_line'
				],
			)
		);

		$this->add_control(
			$this->prefix.'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix.'field_output' => 'type_limeter'
				],
			)
		);

		$this->add_control(
			$this->prefix.'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'condition' => [
					$this->prefix.'field_output' => 'type_array'
				],
			)
		);

		$this->add_control(
			$this->prefix . 'course_clickable',
			[
				'label'        => __( 'Clickable', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

	}


	public function render() {

		$current_product = $this->get_settings( $this->prefix.'current_product' );
		$product_id = $this->get_settings( $this->prefix.'product_id' );
		$return_value = $this->get_settings( $this->prefix.'return_value' );
		$field_output = $this->get_settings( $this->prefix.'field_output' );
		$delimiter = $this->get_settings( $this->prefix.'delimiter' );
		$array_index = $this->get_settings( $this->prefix.'array_index' );
		$clickable = $this->get_settings( $this->prefix . 'course_clickable' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );
		
		global $product;
        
        $course_data = array();
		
		if( is_a($product, 'WC_Product') ) { 

			if($current_product == 'yes') {
				$courses_ids = get_post_meta( get_the_id(), '_related_course', true );
			} else {

				if(!empty($product_id)) {
					$courses_ids = get_post_meta( $product_id, '_related_course', true );
				}
			}
			
			if(is_array($courses_ids) && !empty($courses_ids)) {

				foreach($courses_ids as $course_id) {
					$course_data[] = get_post($course_id);
				}
 	
				if(!empty($course_data)) {
	
					if($return_value == 'slug') {

						foreach($course_data as $course_item) {
							$course_detail[] = array(
								'course_data' => urldecode($course_item->post_name),
								'course_url' => urldecode(get_permalink($course_item->ID))
							);
						}
		
					} elseif($return_value == 'id') {

						foreach($course_data as $course_item) {
							$course_detail[] = array(
								'course_data' => $course_item->ID,
								'course_url' => urldecode(get_permalink($course_item->ID))
							);
						}

					} elseif($return_value == 'link') {
		
						foreach($course_data as $course_item) {
							$course_detail[] = array(
								'course_data' => urldecode(get_permalink($course_item->ID)),
								'course_url' => urldecode(get_permalink($course_item->ID))
							);
						}
		
					} else {
						foreach($course_data as $course_item) {
							$course_detail[] = array(
								'course_data' => $course_item->post_title,
								'course_url' => urldecode(get_permalink($course_item->ID))
							);
						}
					}

					if($clickable == 'yes') {
						foreach($course_detail as $course) {							
							$course_modified[] = '<a href="'.$course['course_url'].'">'.$course['course_data'].'</a>';
						}
					} else {
						foreach($course_detail as $course) {							
							$course_modified[] = $course['course_data'];
						}
					}

					$course_detail = $course_modified;
				}
				
				if (!empty($course_detail) && is_array( $course_detail ) ) {

                    if ( $field_output == 'type_ul' ) {

                        $output .= '<ul class="attribute-ul">';

                        foreach ( $course_detail as $value ) {
                            $output .= '<li>' . $value . '</li>';
                        }

                        $output .= '</ul>';

                    } else if ( $field_output == 'type_ol' ) {

                        $output .= '<ol class="attribute-ol">';

                        foreach ( $course_detail as $value ) {
                            $output .= '<li>' . $value . '</li>';
                        }

                        $output .= '</ol>';

                    } else if ( $field_output == 'type_lenght' ) {

                        $output = count( $course_detail );

                    } else if ( $field_output == 'type_delimiter' && ! empty( $delimiter ) ) {

                        $output = implode( $delimiter, $course_detail );

                    } else if ( $field_output == 'type_array' && ! empty( $array_index ) ) {

                        if ( isset( $course_detail[ $array_index ] ) && ! empty( $course_detail[ $array_index ] ) ) {
                            $output = $course_detail[ $array_index ];
                        }

                    } else if ( $field_output == 'one_per_line' ) {
						if($one_per_line_type == 'html')
							$output = implode( '<br />', $course_detail );
						else
							$output = implode( PHP_EOL, $course_detail );
	
					}

                    echo $output;

                }
			}	
		} 
	}
}