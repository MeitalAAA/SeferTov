<?php
namespace Gloo\Modules\Data_Source;

Class Tag_Maker extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-tag-maker';
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
		return __( 'Google Spreadsheet Tag', 'gloo_for_elementor' );
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
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::NUMBER_CATEGORY
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

		$tags = array();

		$args = array(
			'post_type' => 'gloo_dtm',
			'posts_per_page' => -1,
		);
		
		$posts = get_posts($args);

		if(!empty($posts)) {
			foreach($posts as $dtm) {
				$tags[$dtm->ID] = $dtm->post_title;
			}
		}

		$tags['custom_cpt_id'] = 'Custom ID';
 
		$this->add_control(
			'select_tag',
			array(
				'label'   => __( 'Select Tag', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $tags,
			)
		);

		$this->add_control(
			'is_meta_field',
			[
				'label' => esc_html__( 'Is Meta Field', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'select_tag' => 'custom_cpt_id',
 				]
			]
		);

		$this->add_control(
			'is_relation_field',
			[
				'label' => esc_html__( 'Is Relationship  Field', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'select_tag' => 'custom_cpt_id',
					'is_meta_field' => 'yes'
 				]
			]
		);

		$this->add_control(
			'relation_source',
			array(
				'label'   => __( 'Relation Source', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'acf'        => 'Advanced Custom Fields (ACF)',
					'jet_engine' => 'Jet engine',
					'new_jet_engine_relation' => 'Jet engine 2.11+',
				],
				'condition' => [
					'select_tag' => 'custom_cpt_id',
					'is_meta_field' => 'yes',
					'is_relation_field' => 'yes',
 				],
				'default' => 'acf',
			)
		);
		
		$this->add_control(
			'acf_rel_key',
			array(
				'label'   => __( 'ACF Relation Field Key (Optional)', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'    => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'select_tag' => 'custom_cpt_id',
					'is_meta_field' => 'yes',
					'is_relation_field' => 'yes',
					'relation_source' => 'acf'
				]
			)
		);

		$relation_items = array();

		if(function_exists('jet_engine')) {
			if(method_exists(jet_engine()->relations, 'get_active_relations')) {
				$relations = jet_engine()->relations->get_active_relations();
				if(!empty($relations)) {
					foreach( $relations as $relation) {
						$id = $relation->get_id();
						$relation_name = $relation->get_relation_name();
						$relation_items[$id] = $relation_name;
					}
				}
			}
 			if(!empty($relation_items)) {
				$this->add_control(
					'jet_relation', [
						'label' => __( 'Jet Relation', 'gloo_for_elementor' ),
						'type'        => \Elementor\Controls_Manager::SELECT,
						'options'     => $relation_items,
						'condition' => [
							'relation_source' => 'new_jet_engine_relation',
							'is_relation_field' => 'yes',
							'select_tag' => 'custom_cpt_id',
							'is_meta_field' => 'yes',
						],
					]
				);

				$this->add_control(
					'jet_rel_context', [
						'label' => __( 'Context', 'gloo_for_elementor' ),
						'type'        => \Elementor\Controls_Manager::SELECT,
						'default' => 'child_object',
						'options'     => [
							'child_object'        => 'Child Object',
							'parent_object' => 'Parent Object',
						],
						'condition' => [
							'select_tag' => 'custom_cpt_id',
							'is_meta_field' => 'yes',
							'is_relation_field' => 'yes',
							'relation_source' => 'new_jet_engine_relation'
						],
					]
				);

			}
		}	
		
		$this->add_control(
			'field_context',
			array(
				'label'   => __( 'Field Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'post'        => 'Post',
					'user' => 'User',
					'term' => 'Term',
					'current_user' => 'Current Logged In User',
				],
				'default' => 'post',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'select_tag',
							'operator' => '===',
							'value' => 'custom_cpt_id',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'is_meta_field',
									'operator' => '===',
									'value' => 'yes',
								],
								[
									'name' => 'is_row_meta_field',
									'operator' => '==',
									'value' => 'yes',
								],
								[
									'name' => 'is_column_meta_field',
									'operator' => '==',
									'value' => 'yes',
								],
							],
						],
					],
				],
				// 'condition' => [
				// 	'select_tag' => 'custom_cpt_id',
				// 	'is_meta_field' => 'yes',
				// ],
			)
		);

		$this->add_control(
			'custom_field_key',
			array(
				'label'   => __( 'Field Key', 'gloo_for_elementor' ),
				'label_block' => true,
				'description' => esc_html__( 'This field should return id of single source', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'select_tag' => 'custom_cpt_id',
					'is_meta_field' => 'yes',
					'is_relation_field!' => 'yes',
				]
			)
		);

		$this->add_control(
			'jet_custom_field_key',
			array(
				'label'   => __( 'Jet Field Key', 'gloo_for_elementor' ),
				'label_block' => true,
				'description' => esc_html__( 'This field should return id of single source', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'select_tag' => 'custom_cpt_id',
					'is_meta_field' => 'yes',
					'is_relation_field' => 'yes',
					'relation_source' => 'jet_engine'
				]
			)
		);
  
		$this->add_control(
			'horizontal_cell',
			array(
				'label'     => __( 'Row Cell', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'is_row_meta_field!' => 'yes'
				]
			)
		);

		$this->add_control(
			'is_row_meta_field',
			[
				'label' => esc_html__( 'Fetch value from meta field', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
 			]
		);
		
		
		$this->add_control(
			'row_custom_field_key',
			array(
				'label'   => __( 'Row Field Key', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'    => \Elementor\Controls_Manager::TEXT,
				'condition' => [
 					'is_row_meta_field' => 'yes'
				]
			)
		);

		$this->add_control(
			'vertical_cell',
			array(
				'label'     => __( 'Column Cell', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'is_column_meta_field!' => 'yes'
				]
			)
		);

		$this->add_control(
			'is_column_meta_field',
			[
				'label' => esc_html__( 'Fetch value from meta field', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
 			]
		);
 
		$this->add_control(
			'column_custom_field_key',
			array(
				'label'   => __( 'Column Field Key', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'    => \Elementor\Controls_Manager::TEXT,
				'condition' => [
 					'is_column_meta_field' => 'yes'
				]
			)
		);

		$this->add_control(
			'return_type',
			array(
				'label'   => __( 'Return', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'value'  => esc_html__( 'Value', 'gloo_for_elementor' ),
					'letter_length'  => esc_html__( 'Letters Length', 'gloo_for_elementor' ),
					'is_empty'  => esc_html__( 'Is Empty', 'gloo_for_elementor' ),
				],
				'default' => 'value',
			)
		);
 	}

	public function get_field_context_id() {
		$field_context = $this->get_settings( 'field_context' );
		$relation_source = $this->get_settings( 'relation_source' );
 		$object_id = get_the_ID();

		if( $relation_source == 'acf' ) {
			
			if( $field_context == 'term' ) {
				$object_id = get_queried_object()->taxonomy . '_'. get_queried_object_id();
			}

			if ( is_author() ) {
				$object_id = 'user_'. get_queried_object_id();
			}

			if( $field_context == 'current_user' ) {
				$object_id = 'user_'. get_current_user_id();
			}

		} else {
			
			if( $field_context == 'term' ) {
				$object_id = get_queried_object_id();
			}

			if ( is_author() ) {
				$object_id = get_queried_object_id();
			}

			if( $field_context == 'term' ) {
				$object_id =  get_queried_object_id();
			}
 
			if( $field_context == 'current_user' ) {
				$object_id = get_current_user_id();
			}
		} 

		return $object_id;
	}

	public function get_meta_value_with_context($meta_key) {

		if(empty($meta_key)) {
			return;
		}

		$object_id = $this->get_field_context_id();
		$field_context = $this->get_settings( 'field_context' );
 
		if( $field_context == 'post' ) {
			$value = get_post_meta( $object_id, $meta_key, true );
		} elseif( $field_context == 'user' ||  $field_context == 'current_user') {
			$value = get_user_meta( $object_id, $meta_key, true );
		} 

		return $value;
	}

	public function get_jet_relation_values() {
		$jet_rel_context = $this->get_settings( 'jet_rel_context' );
		$jet_relation = $this->get_settings( 'jet_relation' );
		$field_context = $this->get_settings( 'field_context' );

		$object_id = get_the_ID();
		$related_ids = array();

		if(!empty($jet_relation)) {
			$relation_instance = jet_engine()->relations->get_active_relations( $jet_relation );
 
			$object_id = $this->get_field_context_id($meta_key);
			 
			if(!empty($relation_instance)) {
				switch ( $jet_rel_context ) {
					case 'parent_object':
						$related_ids = $relation_instance->get_parents( $object_id, 'ids' );
						break;
		
					default:
						$related_ids = $relation_instance->get_children( $object_id, 'ids' );
						break;
				}
			}
			
			$related_ids = ! empty( $related_ids ) ? $related_ids : array();
		}
  
		if( isset( $related_ids[0] ) && !empty( $related_ids[0] ) ) {
			$select_tag = $related_ids[0];

			return $select_tag;
		}
	}

	public function get_acf_relationship_values() {
		$acf_rel_key = $this->get_settings( 'acf_rel_key' );
		$field_context = $this->get_settings( 'field_context' );

		$post_data = [];
		$acf_id_prefix = '';
	 
		$acf_id_prefix = $this->get_field_context_id();
		$relationship_values = get_field( $acf_rel_key, $acf_id_prefix );

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
		}

		if( isset( $relationship_ids[0] ) && !empty( $relationship_ids[0] ) ) {
			$select_tag = $relationship_ids[0];

			return $select_tag;
		}
	}

	public function get_jet_depricated_values(){
		$jet_rel_key = $this->get_settings( 'jet_rel_key' );
		$field_context = $this->get_settings( 'field_context' );
		$object_id = $this->get_field_context_id();

		if( $field_context == 'post' ) {
			$relationship_ids = get_post_meta( $object_id, $jet_rel_key, true );
		} elseif( $field_context == 'user' ||  $field_context == 'current_user') {
			$relationship_ids = get_user_meta( $object_id, $jet_rel_key, true );
		} 

		if( isset( $relationship_ids[0] ) && !empty( $relationship_ids[0] ) ) {
			$select_tag = $relationship_ids[0];

			return $select_tag;
		}

 	}

	public function getNameFromNumber($num) {
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return $letter;
		}
	}

	function alphabet_to_number($string) {
		$string = strtoupper($string);
		$length = strlen($string);
		$number = 0;
		$level = 1;
		while ($length >= $level ) {
			$char = $string[$length - $level];
			$c = ord($char) - 64;        
			$number += $c * (26 ** ($level-1));
			$level++;
		}
		return $number;
	}

	public function render() {
		
		$select_tag = $this->get_settings( 'select_tag' );
		$is_meta_field = $this->get_settings( 'is_meta_field' );
		
		$is_relation_field = $this->get_settings( 'is_relation_field' );

		$custom_field_key = $this->get_settings( 'custom_field_key' );
		$is_row_meta_field = $this->get_settings( 'is_row_meta_field' );
		$row_custom_field_key = $this->get_settings( 'row_custom_field_key' );

		$is_column_meta_field = $this->get_settings( 'is_column_meta_field' );
		$column_custom_field_key = $this->get_settings( 'column_custom_field_key' );
		$return_type = $this->get_settings( 'return_type' );
 
		$horizontal_cell = $this->get_settings( 'horizontal_cell' );
		$vertical_cell = $this->get_settings( 'vertical_cell' );
		$acf_rel_key = $this->get_settings( 'acf_rel_key' );
		$field_context = $this->get_settings( 'field_context' );

		if( !empty( $select_tag ) ) {
			if( $select_tag == 'custom_cpt_id' ) {

				if( $is_relation_field == 'yes' && $is_meta_field == 'yes') {
					$relation_source = $this->get_settings( 'relation_source' );
					
					if( !empty( $acf_rel_key ) && $relation_source == 'acf' ) {
						$select_tag = $this->get_acf_relationship_values();
					} elseif ( $relation_source == 'new_jet_engine_relation' ) {
						$select_tag = $this->get_jet_relation_values();
					} elseif ($relation_source == 'jet_engine') {
						$select_tag = $this->get_jet_depricated_values();
					}
				} elseif($is_meta_field == 'yes' && $is_relation_field != 'yes') {
					$custom_field_key = $this->get_settings( 'custom_field_key' );
					$select_tag = $this->get_meta_value_with_context($custom_field_key);
  				}
			}	

			if( $is_row_meta_field == 'yes' ) {
				if( !empty($row_custom_field_key) ) {
 					$horizontal_cell = $this->get_meta_value_with_context($row_custom_field_key);
				}
			}

			if( $is_column_meta_field == 'yes' ) {
				if( !empty($column_custom_field_key) ) {
 					$vertical_cell = $this->get_meta_value_with_context($column_custom_field_key);
				}
			}
 
			$spreadsheet_data =  json_decode(get_post_meta( $select_tag, 'spreadsheet_data', true ));
 			

			//echo '<pre>'; print_r($spreadsheet_data); echo '</pre>';

			if(!empty($spreadsheet_data) && !empty($horizontal_cell) && !empty($vertical_cell) ) {
				$horizontal_index = $horizontal_cell - 1;
				// $vertical_index = $this->get_alphabet_position($vertical_cell);
				$vertical_index = $this->alphabet_to_number($vertical_cell) - 1;
				if(isset($spreadsheet_data[$horizontal_index][$vertical_index])) {
					$sheet_value =  $spreadsheet_data[$horizontal_index][$vertical_index];
				}
			}

			if($return_type == 'letter_length' && !empty($sheet_value )) {
				echo strlen($sheet_value);
			} elseif($return_type == 'is_empty') {
				if(empty($sheet_value)) {
					echo true;
				}
			} else {
				echo $sheet_value;
			}
		}
	}

	public function get_alphabet_position($letter) {
		// Declare an empty array 
		$array = array(); 
	
		for( $i = 65; $i < 91; $i++) {
			$array[] = chr($i); 
		} 

		if(!empty($letter)) {
 			return array_search($letter, $array); // $key = 2;
		}
	}
}