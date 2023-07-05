<?php
namespace Gloo\Modules\Jet_Relation_Dynamic_Tags;

Class Jet_Relation_Tag extends \Elementor\Core\DynamicTags\Tag {

	public $prefix = "jrf_";
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
		return 'jet-relation-dynamic-tags';
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
		return __( 'Jet Relation Field Tag', 'gloo_for_elementor' );
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
		}	

		if(!empty($relation_items)) {
			$this->add_control(
				$this->prefix.'jet_relation', [
					'label' => __( 'Jet Relation', 'gloo_for_elementor' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $relation_items,
				]
			);
		}

		$this->add_control(
			$this->prefix.'jet_rel_context', [
				'label' => __( 'Object', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default' => 'child_object',
				'options'     => [
					'child_object'        => 'Child Object',
					'parent_object' => 'Parent Object',
				],
			]
		);

		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
 			'type_limeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		$context_type = [
			'post'      => 'Post',
			'term'      => 'Term',
 			'user' => 'User',
		];

		$this->add_control(
			$this->prefix.'context_type',
			array(
				'label'   => __( 'Object Relation', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => $context_type,
			)
		);
    
		$this->add_control(
			$this->prefix.'field_output',
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
			$this->prefix.'data_index',
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
							'name' => $this->prefix.'field_output',
							'operator' => '==',
							'value' => 'type_array'
						],
					]
				]
			)
		);

		$this->add_control(
			$this->prefix.'array_index',
			array(
				'label'     => __( 'Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix.'field_output',
							'operator' => '==',
							'value' => 'type_array'
						],
						[
							'name' => $this->prefix.'data_index',
							'operator' => '==',
							'value' => 'specific_index'
						],
					]
				]
			)
		);

		$this->add_control(
			$this->prefix.'user_field',
			array(
				'label'   => __( 'User Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'nickname',
				'options' => array(
					'nickname' => __( 'Nickname', 'gloo_for_elementor' ),
					'email' => __( 'Email', 'gloo_for_elementor' ),
					'username'=> __( 'Username', 'gloo_for_elementor' ),
					'user_meta' => __( 'Meta Field', 'gloo_for_elementor' ),
				),
				'condition' => [
					$this->prefix.'context_type' => ['user'],
				]
			)
		);

		$this->add_control(
			$this->prefix.'term_field',
			array(
				'label'   => __( 'Term Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'term_id',
				'options' => array(
					'term_id' => __( 'ID', 'gloo_for_elementor' ),
					'term_slug' => __( 'Slug', 'gloo_for_elementor' ),
					'term_name' => __( 'Title', 'gloo_for_elementor' ),
					'description' => __( 'Description', 'gloo_for_elementor' ),
					'term_link' => __( 'Link', 'gloo_for_elementor' ),
					'term_meta' => __( 'Meta Field', 'gloo_for_elementor' ),
				),
				'condition' => [
					$this->prefix.'context_type' => ['term'],
				]
			)
		);

		$this->add_control(
			$this->prefix.'post_field',
			array(
				'label'   => __( 'Post Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post_id',
				'options' => array(
					'post_id' => __( 'ID', 'gloo_for_elementor' ),
					'post_slug' => __( 'Slug', 'gloo_for_elementor' ),
					'post_title' => __( 'Title', 'gloo_for_elementor' ),
					'post_link' => __( 'Link', 'gloo_for_elementor' ),
					'post_meta' => __( 'Meta Field', 'gloo_for_elementor' ),
				),
				'condition' => [
					$this->prefix.'context_type' => ['post'],
				]
			)
		);

		$this->add_control(
			$this->prefix.'user_meta_key',
			array(
				'label'     => __( 'User Meta Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix.'user_field' => 'user_meta',
					$this->prefix.'context_type' => ['user']
				]
			)
		);

		$this->add_control(
			$this->prefix.'term_meta_key',
			array(
				'label'     => __( 'Term Meta Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix.'term_field' => 'term_meta',
					$this->prefix.'context_type' => ['term']
				]
			)
		);

		$this->add_control(
			$this->prefix.'post_meta_key',
			array(
				'label'     => __( 'Post Meta Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix.'context_type',
							'operator' => '!in',
							'value' => [
								'term',
								'user'
							]
						],
						[
							'name' => $this->prefix.'post_field',
							'operator' => '==',
							'value' => 'post_meta'
						],
					]
				],
			)
		);
		
		$this->add_control(
			$this->prefix.'enable_zero',
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

	public function get_jet_relation_values() {
		$jet_relation = $this->get_settings( $this->prefix.'jet_relation' );
		$jet_rel_context = $this->get_settings( $this->prefix.'jet_rel_context' );
		$related_ids = array();

		if(!empty($jet_relation)) {
			$relation_instance = jet_engine()->relations->get_active_relations( $jet_relation );

			$object_id = get_the_ID();

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

		return $related_ids;
	}

	
	public function get_rendered_output( $data = array() ) {		
		
		$field_output = $this->get_settings( $this->prefix.'field_output' );
		$delimiter = $this->get_settings( $this->prefix.'delimiter' );
		$array_index     = $this->get_settings( $this->prefix.'array_index' );
		$data_index = $this->get_settings( $this->prefix.'data_index' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		if( !empty( $data ) && is_array( $data ) ) {
			
			$output .= '';

			if ( $field_output == 'type_ul' ) {

				$output .= '<ul class="tax-ul">';

				foreach ( $data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ul>';

			} else if ( $field_output == 'type_ol' ) {

				$output .= '<ol class="tax-ol">';

				foreach ( $data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ol>';


			} else if ( $field_output == 'type_lenght' ) {

				$output = count( $data );

			} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

				$output = implode( $delimiter, $data );

			} else if ( $field_output == 'type_array' ) {
				
				if( $data_index == 'specific_index'  && is_numeric($array_index) ) {
					if ( isset( $data[ $array_index ] ) && ! empty( $data[ $array_index ] ) ) {
						$output = $data[ $array_index ];
					}
				} elseif( $data_index ==  'first_index' ) {
					
					$firstKey = array_key_first($data);
					$output = $data[$firstKey];

				} elseif( $data_index ==  'last_index' ) {
					$output = end($data);
				}
			}
			else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $data );
				else
					$output = implode( PHP_EOL, $data );

			}

			echo $output;
		}
	}

	
	public function get_post_native_field( $post_field, $posts_object = array()) {

		if( empty( $post_field ) ) {
			return;
		}
		
		if( $posts_object ) {

			if ( $post_field == 'post_slug' ) {

				foreach ( $posts_object as $post_item ) {
					$posts_data[] = urldecode( $post_item->post_name );
				}

			} else if ( $post_field == 'post_id' ) {
				foreach ( $posts_object as $post_item ) {
					$posts_data[] = $post_item->ID;
				}
			} else if ( $post_field == 'post_link' ) {

				foreach ( $posts_object as $post_item ) {
					$posts_data[] = urldecode( get_permalink( $post_item->ID ) );
				}

			} else if( $post_field == 'post_title' ) {
				foreach ( $posts_object as $post_item ) {
					$posts_data[] = $post_item->post_title;
				}
			} else {
				$post_meta_key    = $this->get_settings( $this->prefix.'post_meta_key' );

				if( !empty( $post_meta_key ) ) {
					
					foreach ( $posts_object as $post_item ) {
						$post_meta = get_post_meta( $post_item->ID, $post_meta_key, true );
						$posts_data[] = $post_meta;
					}
				}
			}

			return $posts_data;
		}
	}
	
	public function get_user_native_field( $user_field, $user_object ) {
		
		if( empty( $user_field ) || empty( $user_object ) ) {
			return;
		}
 		
		if( $user_field == 'nickname' ) {
			return $user_object->data->user_nicename;
		} elseif( $user_field == 'email' ) {
			return $user_object->data->user_email;
		} elseif( $user_field == 'username' ) {
			return $user_object->data->user_login;
		}
	}

	public function get_user_metadata_value( $user_object, $user_meta_key ) {
		
		if( empty( $user_meta_key ) || empty( $user_object) ) {
			return;
		}
		
		$field_value = get_user_meta($user_object->ID, $user_meta_key, true);
		return $field_value;
	}

	/* get user values */
	public function get_user_data_value($user_field, $user_object, $user_meta_key = '') {
		$output = array();

		if(empty($user_object)) {
			return;
		}
		
		if( $user_field == 'user_meta' ) {
			foreach( $user_object as $object) {
				$output[] = $this->get_user_metadata_value( $object, $user_meta_key );
			}
		} else {
			foreach( $user_object as $object) {
				$output[] =  $this->get_user_native_field( $user_field, $object );
			}
		}

		return $output;
	}
	
	public function get_term_metadata_value( $term_object, $term_meta_key ) {
		
		if( empty( $term_meta_key ) || empty( $term_object) ) {
			return;
		}
		
		$term_value = get_term_meta( $term_object->term_id, $term_meta_key, true );
		return $term_value;
	}

	public function get_term_data_value($term_field, $term_object, $term_meta_key = '') {
		$output = array();

		if(empty($term_object)) {
			return;
		}

		if( $term_field == 'term_meta' ) {
			foreach( $term_object as $object) {
				$output[] = $this->get_term_metadata_value( $object, $term_meta_key );
			}
		} else {
			foreach( $term_object as $object) {
				$output[] =  $this->get_term_native_field( $object, $term_field );
			}
		}

		return $output;
	}
	
	public function get_term_native_field( $term_object, $term_field ) {
	
		if( empty( $term_field) || empty( $term_object ) ) {
			return;
		}
		
		if( $term_field == 'term_id' ) {
			return $term_object->term_id;
		} elseif( $term_field == 'term_slug' ) {
			return $term_object->slug;
		} elseif( $term_field == 'term_name' ) {
			return $term_object->name;
		} elseif( $term_field == 'description' ) {
			return $term_object->description;
		} elseif( $term_field == 'term_link' ) {
			return get_term_link( $term_object );
		}
		
	}

	public function render() {
  
		$context_type = $this->get_settings( $this->prefix.'context_type' );

		/* meta option */
		$user_field    = $this->get_settings( $this->prefix.'user_field' );
		$term_field    = $this->get_settings( $this->prefix.'term_field' );
		$post_field    = $this->get_settings( $this->prefix.'post_field' );
		
		/* meta keys */
		$user_meta_key    = $this->get_settings( $this->prefix.'user_meta_key' );
		$term_meta_key    = $this->get_settings( $this->prefix.'term_meta_key' );
		$post_meta_key    = $this->get_settings( $this->prefix.'post_meta_key' );

		$values = $this->get_jet_relation_values();

		if(!empty($values)) {
			switch ( $context_type ) {

				case 'user':	
					foreach( $values as $user_id ) {
						$user_object[]  = get_user_by('id', $user_id);
					}

					if(!empty($user_object)) {
						$user_data = $this->get_user_data_value($user_field, $user_object, $user_meta_key);
						$this->get_rendered_output($user_data); 
					}
					break;
				case 'term':	
					foreach( $values as $term_id ) {
						$term_object[] = get_term($term_id);
					}
					
					if(!empty($term_object)) {
						$user_data = $this->get_term_data_value($term_field, $term_object, $term_meta_key);
						$this->get_rendered_output($user_data);
					}
					break;
				case 'post':	
					foreach( $values as $post_id ) {
						$posts_object[] = get_post( $post_id );
					}

					if(!empty($posts_object)) {
						$post_data = $this->get_post_native_field($post_field, $posts_object);
						$this->get_rendered_output($post_data); 
					}
					break;	
			}
		}
 	}
}