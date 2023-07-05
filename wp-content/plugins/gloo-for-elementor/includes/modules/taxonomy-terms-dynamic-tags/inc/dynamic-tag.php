<?php

namespace Gloo\Modules\Taxonomy_Terms_Dynamic_Tags;

Class Taxonomy_Tags extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-taxonomy-tags';
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
		return __( 'Taxonomy Tags', 'gloo_for_elementor' );
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

		$labels = [];
		$tax_args = [
			'public' => true,
		];

		$taxonomies = get_taxonomies($tax_args);

		if(!empty($taxonomies)) {
			foreach ($taxonomies as $tax) {
				$tax_info = get_taxonomy($tax);
				$labels[$tax] = $tax_info->label;
			}
		}

		$this->add_control(
			'select_taxonomy',
			array(
				'label'   => __( 'Select Taxonomy', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $labels,
			)
		);

		$this->add_control(
			'tax_specific_term',
			[
				'label' => esc_html__( 'Specific Term Children', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'tax_term_id',
			array(
				'label'     => __( 'Parent ID\'s', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'tax_specific_term' => 'yes'
				],
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

		$return_value = [
			'title'      => 'Title',
			'slug'      => 'Slug',
			'id' => 'ID',
			'link'  => 'Link',
			'clickable'   => 'Clickable Title'
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

		$return_value = $this->get_settings( 'return_value' );
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$array_index = $this->get_settings( 'array_index' );
		$select_taxonomy = $this->get_settings( 'select_taxonomy' );
		$tax_specific_term = $this->get_settings( 'tax_specific_term' );
		$tax_term_id = $this->get_settings( 'tax_term_id' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );
		
		global $post;
		$terms_data = [];
		
		if(!empty($select_taxonomy)) {
			if($tax_specific_term == 'yes') {
				$post_terms = get_terms( $select_taxonomy, 
					array(
						'parent' => $tax_term_id , 
						'depth'=> 1
					)
				);
			} else {
				$post_terms = wp_get_post_terms($post->ID, $select_taxonomy, array('fields' => 'all'));
			}
			
			if($return_value == 'slug') {

				foreach($post_terms as $term) {
					$terms_data[] = $term->slug;
				}

			} elseif($return_value == 'id') {

				foreach($post_terms as $term) {
					$terms_data[] = $term->term_id;
				}

			} elseif($return_value == 'link') {

				foreach($post_terms as $term) {
					$terms_data[] = get_term_link($term->term_id);
				}

			} elseif($return_value == 'clickable') {

				foreach($post_terms as $term) {
					$terms_data[] = '<a href="'.get_term_link($term->term_id).'">'.$term->name.'</a>';
				}

			} else {
				foreach($post_terms as $term) {
					$terms_data[] = $term->name;
				}
			}

			$output = '';

			if(!empty($terms_data) && is_array($terms_data)) {

				if ( $field_output == 'type_ul' ) {

					$output .= '<ul class="tax-ul">';
					foreach ( $terms_data as $value ) {
						$output .= '<li>' . $value . '</li>';
					}
					$output .= '</ul>';

				} else if ( $field_output == 'type_ol' ) {

					$output .= '<ol class="tax-ol">';
					foreach ( $terms_data as $value ) {
						$output .= '<li>' . $value . '</li>';
					}
					$output .= '</ol>';

				} else if ( $field_output == 'type_lenght' ) {

					$output = count( $terms_data );

				} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

					$output = implode( $delimiter, $terms_data );

				} else if ( $field_output == 'type_array' && is_numeric($array_index) ) {

					if ( isset( $terms_data[ $array_index ] ) && ! empty( $terms_data[ $array_index ] ) ) {
						$output = $terms_data[ $array_index ];
					}
				}
				else if ( $field_output == 'one_per_line' ) {
					if($one_per_line_type == 'html')
						$output = implode( '<br />', $terms_data );
					else
						$output = implode( PHP_EOL, $terms_data );

				}

				echo $output;
			}
		} 
	}
}