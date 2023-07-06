<?php
namespace Gloo\Modules\Acf_Dynamic_Tags;

Class Acf_Relation_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'acf-dynamic-tags';
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
		return __( 'ACF Relation Field Tag', 'gloo_for_elementor' );
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
			'relation_key',
			array(
				'label'   => __( 'Relation Field Key', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type field key here', 'gloo_for_elementor' ),
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
			'name'      => 'Name',
			'slug'      => 'Slug',
 			'id' => 'ID',
			'link'  => 'Link',
			'clickable'   => 'Clickable Title',
			'meta_field' => 'Meta Field'
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
			'relationship_tag_meta_key',
			array(
				'label'     => __( 'Meta Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'return_value' => 'meta_field'
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
			'data_index',
			array(
				'label'   => __( 'Array Index', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'specific_index',
				'options' => array(
					'specific_index' => __( 'Specific Index', 'gloo_for_elementor' ),
					'first_index' => __( 'First Index', 'gloo_for_elementor' ),
					'last_index' => __( 'Last Index', 'gloo_for_elementor' ),
				),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_output',
							'operator' => '==',
							'value' => 'type_array'
						],
					]
				]
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_output',
							'operator' => '==',
							'value' => 'type_array'
						],
						[
							'name' => 'data_index',
							'operator' => '==',
							'value' => 'specific_index'
						]
					]
				]
			)
		);
		
		$this->add_control(
			'enable_zero',
			[
				'label' => __( 'Enable Zero', 'gloo' ),
				'description' => __( 'if empty return 0', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo' ),
				'label_off' => __( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

	}


	public function render() {
		
		$relation_key = $this->get_settings( 'relation_key' );
		$return_value = $this->get_settings( 'return_value' );
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$enable_zero = $this->get_settings( 'enable_zero' );
		$array_index     = $this->get_settings( 'array_index' );
		$data_index = $this->get_settings( 'data_index' );
		$relationship_tag_meta_key = $this->get_settings( 'relationship_tag_meta_key' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		if(!empty($relation_key)) {
	
			$post_data = [];

			$acf_id_prefix = '';
		
			if ( is_tax() || is_category()) {
				$acf_id_prefix = get_queried_object()->taxonomy . '_';
			}
			if ( is_author() ) {
				$acf_id_prefix = 'user_';
			}

			$gloo_current_object_id = get_queried_object_id();
			if(function_exists("jet_engine") && isset(jet_engine()->listings) && isset(jet_engine()->listings->data) && jet_engine()->listings->data->get_current_object() && isset(jet_engine()->listings->data->get_current_object()->ID)){
				$gloo_current_object_id = jet_engine()->listings->data->get_current_object()->ID;
				$acf_id_prefix = '';
			}
			$relationship_values = get_field( $relation_key, $acf_id_prefix . $gloo_current_object_id );

			if(!empty($relationship_values)) {
				
			
				$relationship_ids = [];
				if ( $relationship_values && is_array( $relationship_values ) ) {
					// ACF returned object
					if ( isset( $relationship_values[0] ) && is_object( $relationship_values[0] ) && isset( $relationship_values[0]->ID ) ) {

						foreach ( $relationship_values as $relationship_value ) {
							$relationship_ids[] = $relationship_value->ID;
						}
					} else {
						// ACF returned IDs
						$relationship_ids = $relationship_values;
					}
				}

				if($return_value == 'slug') {

					foreach($relationship_ids as $id) {
						$post = get_post($id); 
						$post_data[] = urldecode($post->post_name);
					}

				} elseif($return_value == 'id') {
					
					foreach($relationship_ids as $id) {
						$post_data[] = $id;
					}
					
				} elseif($return_value == 'link') {

					foreach($relationship_ids as $id) {
						$post_data[] = get_permalink($id);
					}
				
				} elseif($return_value == 'clickable') {

					foreach($relationship_ids as $id) {
						$post_data[] = '<a href="'.get_permalink($id).'">'.get_the_title($id).'</a>';
					}

				} elseif($return_value == 'meta_field' && !empty($relationship_tag_meta_key)){
					
					foreach($relationship_ids as $id) {
						$post_data[] = get_post_meta($id, $relationship_tag_meta_key, true);
					}

				} else {
					foreach($relationship_ids as $id) {
						$post_data[] = get_the_title($id);
					}
				}

				$output = '';

				if(!empty($post_data) && is_array($post_data)) {

					if ( $field_output == 'type_ul' ) {

						$output .= '<ul class="tax-ul">';

						foreach ( $post_data as $value ) {
							$output .= '<li>' . $value . '</li>';
						}

						$output .= '</ul>';

					} else if ( $field_output == 'type_ol' ) {

						$output .= '<ol class="tax-ol">';

						foreach ( $post_data as $value ) {
							$output .= '<li>' . $value . '</li>';
						}

						$output .= '</ol>';
		

					} else if ( $field_output == 'type_lenght' ) {

						$output = count( $post_data );

					} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

						$output = implode( $delimiter, $post_data );

					}else if ( $field_output == 'type_array' /*&& ! empty( $array_index )*/ ) {

						if( $data_index == 'specific_index'  && is_numeric($array_index) ) {
							if ( isset( $post_data[ $array_index ] ) && ! empty( $post_data[ $array_index ] ) ) {
								$output = $post_data[ $array_index ];
							}
						} elseif( $data_index ==  'first_index' ) {
							
							$firstKey = array_key_first($post_data);
							$output = $post_data[$firstKey];
		
						} elseif( $data_index ==  'last_index' ) {
							$output = end($post_data);
						}

						// if ( isset( $post_data[ $array_index ] ) && ! empty( $post_data[ $array_index ] ) ) {
						// 	$output = $post_data[ $array_index ];
						// }

					}
					else if ( $field_output == 'one_per_line' ) {
					if($one_per_line_type == 'html')
						$output = implode( '<br />', $post_data );
					else
						$output = implode( PHP_EOL, $post_data );
				}

					echo $output;	

				}

			} else {
				if($enable_zero == 'yes') {
					echo '0';
				} else {
					return;
				}
			}

		}
	
 	}
	
}