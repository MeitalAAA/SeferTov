<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;
use Jet_Engine\Query_Builder\Manager;

Class Context_Dynamic extends \Elementor\Core\DynamicTags\Tag {

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
		return 'context-dynamic';
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
		return __( 'Context Dynamic Tag', 'gloo_for_elementor' );
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
			\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY
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

		$tag_content = array(
			'current_term' => __( 'Current Term', 'gloo_for_elementor' ),
			'related_term' => __( 'Related Terms', 'gloo_for_elementor' ),
			'current_post' => __( 'Current Post', 'gloo_for_elementor' ),
			'current_user' => __( 'Current User', 'gloo_for_elementor' ),
			'current_author' => __( 'Current Author', 'gloo_for_elementor' ),
			'queried_post_author' => __( 'Queried Post Author', 'gloo_for_elementor' ),
		);

		if(function_exists('buddypress')) {
			$tag_content['displayed_user'] = __( 'BB Displayed User', 'gloo_for_elementor' );
		}
		

		if(function_exists( 'jet_engine' )) {
			$tag_content['queried_user'] = __( 'Queried User', 'gloo_for_elementor' );
			$tag_content['jet_query_builder'] = __('Jet Query Builder');
		}
 	
		$this->add_control(
			'tag_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => $tag_content
			)
		);
		
		if(function_exists( 'jet_engine' )) {
 
			$this->add_control(
				'jet_query_id',
				array(
					'label'   => __( 'Jet Query', 'gloo_for_elementor' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'current_user',
					'options' => Manager::instance()->get_queries_for_options(),
					'condition' => [
						'tag_context' => 'jet_query_builder',
					]
				)
			);

			$this->add_control(
				'jet_array_level',
				array(
					'label'     => __( 'Array Level', 'gloo_for_elementor' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'level_1',
					'options' => [
						'level_1' => '1',
						'level_2' => '2',
						'level_3' => '3'
					],
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'tag_context',
								'operator' => 'in',
								'value' =>  ['jet_query_builder']
							],
							[
								'name' => 'jet_query_id',
								'operator' => '!=',
								'value' =>  ''
							],
						]
					]
				)
			);
			
			$this->add_control(
				'jet_query_level_1_key',
				array(
					'label'     => __( 'Jet Query Level 1 Key', 'gloo_for_elementor' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'tag_context',
								'operator' => 'in',
								'value' =>  ['jet_query_builder']
							],
							[
								'name' => 'jet_query_id',
								'operator' => '!=',
								'value' =>  ''
							],
							[
								'name' => 'jet_array_level',
								'operator' => 'in',
								'value' =>  ['level_1','level_2', 'level_3']
							]
						]
					],
					'dynamic'     => [
						'active' => true,
					],
				)
			);
 
			$this->add_control(
				'jet_query_level_2_key',
				array(
					'label'     => __( 'Jet Query Level 2 Key', 'gloo_for_elementor' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'tag_context',
								'operator' => 'in',
								'value' =>  ['jet_query_builder']
							],
							[
								'name' => 'jet_query_id',
								'operator' => '!=',
								'value' =>  ''
							],
							[
								'name' => 'jet_array_level',
								'operator' => 'in',
								'value' =>  ['level_2', 'level_3']
							]
						]
					],
					'dynamic' => [
						'active' => true,
					],
				)
			);

			$this->add_control(
				'jet_query_level_3_key',
				array(
					'label'     => __( 'Jet Query Level 3 Key', 'gloo_for_elementor' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'tag_context',
								'operator' => 'in',
								'value' =>  ['jet_query_builder']
							],
							[
								'name' => 'jet_query_id',
								'operator' => '!=',
								'value' =>  ''
							],
							[
								'name' => 'jet_array_level',
								'operator' => 'in',
								'value' =>  ['level_3']
							]
						]
					],
					'dynamic'     => [
						'active' => true,
					],
				)
			);
		}
 
		$this->add_control(
			'show_parent',
			[
				'label' => __( 'Parent Term Only', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo_for_elementor' ),
				'label_off' => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'tag_context' => ['current_term', 'related_term'],
				]
			]
		);
 
		$this->add_control(
			'context_tax_specific_term',
			[
				'label' => esc_html__( 'Specific Term Children', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_parent',
							'operator' => '!=',
							'value' => 'yes'
						],
						[
							'name' => 'tag_context',
							'operator' => 'in',
							'value' =>  ['current_term', 'related_term']
						]
					]
				]
			]
		);
		
		$this->add_control(
			'context_tax_term_id',
			array(
				'label'     => __( 'Parent ID\'s', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'show_parent',
							'operator' => '!=',
							'value' => 'yes'
						],
						[
							'name' => 'context_tax_specific_term',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'tag_context',
							'operator' => 'in',
							'value' =>  ['current_term', 'related_term']
						]
					]
				]
			)
		);

		$post_types = array();
		$types = get_post_types( [], 'objects' );

		foreach ( $types as $type ) {
			$post_types[$type->name] = $type->label;
		}

		/* post types */
		$this->add_control(	
			'post_type',
			array(
				'label'   => __( 'Post Types', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => 'post',
				'options' => $post_types,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'tag_context',
							'operator' => '!in',
							'value' => [
								'current_term',
								'current_post',
								'related_term'
							]
						],
						[
							'name' => 'user_field',
							'operator' => '==',
							'value' => 'related_posts'
						],
					]
				]
			)
		);

		/* terms controls */
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
				'condition' => [
					'tag_context' => 'related_term',
				]
			)
		);
	
		$this->add_control(
			'user_field',
			array(
				'label'   => __( 'User Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'nickname',
				'options' => array(
					'nickname' => __( 'Nickname', 'gloo_for_elementor' ),
					'email' => __( 'Email', 'gloo_for_elementor' ),
					'username'=> __( 'Username', 'gloo_for_elementor' ),
					'related_posts' => __( 'Related Posts', 'gloo_for_elementor' ),
					'user_meta' => __( 'Meta Field', 'gloo_for_elementor' ),
				),
				'condition' => [
					'tag_context' => ['current_user','current_author','queried_user','queried_post_author', 'displayed_user'],
				]
			)
		);

		/* end term control */
		$this->add_control(
			'term_field',
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
					'tag_context' => ['current_term','related_term'],
				]
			)
		);

		/* added for object post meta value */
		// $this->add_control(
		// 	'is_term_meta_object',
		// 	[
		// 		'label' => __( 'Is Meta Object ?', 'gloo' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'label_on' => __( 'Yes', 'gloo' ),
		// 		'label_off' => __( 'No', 'gloo' ),
		// 		'return_value' => 'yes',
		// 		'default' => 'no',
		// 		'conditions' => [
		// 			'relation' => 'and',
		// 			'terms' => [
		// 				[
		// 					'name' => 'tag_context',
		// 					'operator' => 'in',
		// 					'value' => [
		// 						'current_term',
		// 						'related_term',
		// 					]
		// 				],
		// 				[
		// 					'name' => 'term_field',
		// 					'operator' => '==',
		// 					'value' => 'term_meta'
		// 				],
		// 			]
		// 		]
		// 	]
		// );

		$this->add_control(
			'post_field',
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
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'tag_context',
							'operator' => '!in',
							'value' => [
								'current_term',
								'related_term'
							]
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'user_field',
									'operator' => '==',
									'value' => 'related_posts'
								],
								[
									'name' => 'tag_context',
									'operator' => '==',
									'value' => 'current_post'
								]
							]
						],
					]
				]
			)
		);

			
		/* added for object user meta value */
		$this->add_control(
			'is_meta_object',
			[
				'label' => __( 'Is Meta Object ?', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo' ),
				'label_off' => __( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'no',
				'conditions' => [
					'terms' => [
						[
							'name' => 'tag_context',
							'operator' => '!in',
							'value' => [
								'jet_query_builder',
							]
						],
					]
				]
			]
		);

		/* added for object post meta value */
		// $this->add_control(
		// 	'is_post_meta_object',
		// 	[
		// 		'label' => __( 'Is Meta Object ?', 'gloo' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'label_on' => __( 'Yes', 'gloo' ),
		// 		'label_off' => __( 'No', 'gloo' ),
		// 		'return_value' => 'yes',
		// 		'default' => 'no',
		// 		'conditions' => [
		// 			'relation' => 'and',
		// 			'terms' => [
		// 				[
		// 					'name' => 'tag_context',
		// 					'operator' => 'in',
		// 					'value' => [
		// 						'current_post',
		// 					]
		// 				],
		// 				[
		// 					'name' => 'post_field',
		// 					'operator' => '==',
		// 					'value' => 'post_meta'
		// 				],
		// 			]
		// 		]
		// 	]
		// );
		
		$context_type = [
			'post'      => 'Post',
			'term'      => 'Term',
 			'user' => 'User',
		];

		$this->add_control(
			'object_relation',
			array(
				'label'   => __( 'Object Relation', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => $context_type,
				'condition' => [
					'is_meta_object' => 'yes'
				]
			)
		);

		/* meta object fields */
		$this->add_control(
			'meta_user_field',
			array(
				'label'   => __( 'Meta User Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'nickname',
				'options' => array(
					'nickname' => __( 'Nickname', 'gloo_for_elementor' ),
					'email' => __( 'Email', 'gloo_for_elementor' ),
					'username'=> __( 'Username', 'gloo_for_elementor' ),
				),
				'condition' => [
					'object_relation' => 'user',
					'is_meta_object' => 'yes'
				]
			)
		);

 		$this->add_control(
			'meta_term_field',
			array(
				'label'   => __( 'Meta Term Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'term_id',
				'options' => array(
					'term_id' => __( 'ID', 'gloo_for_elementor' ),
					'term_slug' => __( 'Slug', 'gloo_for_elementor' ),
					'term_name' => __( 'Title', 'gloo_for_elementor' ),
					'description' => __( 'Description', 'gloo_for_elementor' ),
					'term_link' => __( 'Link', 'gloo_for_elementor' ),
				),
				'condition' => [
					'object_relation' => 'term',
					'is_meta_object' => 'yes'
				]
			)
		);
 
		$this->add_control(
			'meta_post_field',
			array(
				'label'   => __( 'Meta Post Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post_id',
				'options' => array(
					'post_id' => __( 'ID', 'gloo_for_elementor' ),
					'post_slug' => __( 'Slug', 'gloo_for_elementor' ),
					'post_title' => __( 'Title', 'gloo_for_elementor' ),
					'post_link' => __( 'Link', 'gloo_for_elementor' ),
				),
				'condition' => [
					'object_relation' => 'post',
					'is_meta_object' => 'yes'
				]
			)
		);
		
		$this->add_control(
			'is_meta_image', 
			array(
				'label' => __( 'Image Meta Field?', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
				'description'  => __('it returns the image url'),
				'condition'   => [
					'is_meta_object!' => 'yes',	
					'term_field' => 'term_meta',
				],
			)
		);

		$this->add_control(
			'user_meta_key',
			array(
				'label'     => __( 'User Meta Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'user_field' => 'user_meta',
					'tag_context' => ['current_user','current_author','queried_user','queried_post_author', 'displayed_user']
				]
			)
		);

		$this->add_control(
			'term_meta_key',
			array(
				'label'     => __( 'Term Meta Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'term_field' => 'term_meta',
					'tag_context' => [ 'current_term', 'related_term' ]
				]
			)
		);

		$this->add_control(
			'post_meta_key',
			array(
				'label'     => __( 'Post Meta Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'tag_context',
							'operator' => '!in',
							'value' => [
								'current_term',
								'related_term'
							]
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'user_field',
									'operator' => '==',
									'value' => 'related_posts'
								],
								[
									'name' => 'tag_context',
									'operator' => '==',
									'value' => 'current_post'
								]
							]
						],
						[
							'name' => 'post_field',
							'operator' => '==',
							'value' => 'post_meta'
						],
					]
				],
			)
		);

		/* output options */
		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_limeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line',
			'custom_output'   => 'Custom',
		];

		$this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_none',
				'options' => $output_option,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'user_field',
							'operator' => '==',
							'value' => 'related_posts'
						],
						[
							'name' => 'tag_context',
							'operator' => 'in',
							'value' => ['related_term','jet_query_builder']
						],
						[
							'name' => 'is_meta_object',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			)
		);

		$this->add_control(
			'custom_before_tag',
			array(
				'label'     => __( 'Opening Tag', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Example: <div class="item-class">', 'gloo_for_elementor' ),
				'condition' => [
					'field_output' => 'custom_output'
				],
			)
		);

		$this->add_control(
			'custom_after_tag',
			array(
				'label'     => __( 'Closing Tag', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Example: </div>', 'gloo_for_elementor' ),
				'condition' => [
					'field_output' => 'custom_output'
				],
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
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_output',
							'operator' => '==',
							'value' => 'type_limeter'
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'user_field',
									'operator' => '==',
									'value' => 'related_posts'
								],
								[
									'name' => 'tag_context',
									'operator' => 'in',
									'value' => ['related_term','jet_query_builder']
								],
								[
									'name' => 'is_meta_object',
									'operator' => '==',
									'value' => 'yes'
								]
							]
						]
					]
				]
			)
		);

		$this->add_control(
			'data_index',
			array(
				'label'   => __( 'Array Index', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
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
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'user_field',
									'operator' => '==',
									'value' => 'related_posts'
								],
								[
									'name' => 'tag_context',
									'operator' => 'in',
									'value' => ['related_term','jet_query_builder']
								],
								[
									'name' => 'is_meta_object',
									'operator' => '==',
									'value' => 'yes'
								]
							]
						]
					]
				]
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Index Value', 'gloo_for_elementor' ),
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
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' => 'user_field',
									'operator' => '==',
									'value' => 'related_posts'
								],
								[
									'name' => 'tag_context',
									'operator' => 'in',
									'value' => ['related_term','jet_query_builder']
								],
								[
									'name' => 'is_meta_object',
									'operator' => '==',
									'value' => 'yes'
								]
							]
						]
					]
				]
			)
		);
 
	}

	public function get_post_metadata() {
		$post_id = get_the_ID();
		$items   = get_post_meta( $post_id, $field, true );
	}

	public function get_user_metadata_value( $user_object, $user_meta_key ) {
		
		if( empty( $user_meta_key ) || empty( $user_object) ) {
			return;
		}
		
		$field_value = get_user_meta($user_object->ID, $user_meta_key, true);
		return $field_value;
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

	public function get_post_navtive_field( $post_field, $posts_object = array(), $array = false ) {

		if( empty( $post_field ) ) {
			return;
		}

		if( ! $array ) {

			$post_id = get_the_ID();
			$post = get_post( $post_id );
	
			if( !empty( $post ) ) {
	
				if( $post_field == 'post_id' ) {
					return $post->ID;
				} elseif( $post_field == 'post_slug' ) {
					return $post->post_name;
				} elseif( $post_field == 'post_title' ) {
					return $post->post_title;
				} elseif( $post_field == 'post_link' ) {
					return get_permalink( $post );
				}
			}
		} else if( $array ) {

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
				$post_meta_key    = $this->get_settings( 'post_meta_key' );

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

	public function get_user_navtive_field( $user_field, $user_object ) {
		
		if( empty( $user_field ) && empty( $user_object ) ) {
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

	public function get_rendered_output( $data = array() ) {		
		
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$data_index = $this->get_settings( 'data_index' );
		$array_index = $this->get_settings( 'array_index' );

		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		$settings = $this->get_settings_for_display();

		if( !empty( $data ) && is_array( $data ) ) {
			// echo '<pre>'; print_r($data); echo '</pre>';
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
			} else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $data );
				else
					$output = implode( PHP_EOL, $data );

			} else if ( $field_output == 'custom_output' ) {
				$custom_before_tag = $settings['custom_before_tag'];
				$custom_after_tag = $settings['custom_after_tag'];
				$output = '';

				foreach ( $data as $value ) {
					$output .= $custom_before_tag . $value . $custom_after_tag;
				}
			}

			return $output;
		}
	}

	public function get_parent_term( $term_object ) {

		if( empty( $term_object ) ) {
			return;
		}

		if( $term_object->parent == 0 ) {
			return $term_object;
		}

		while ( $term_object->parent != '0' ) {
			$term_id = $term_object->parent;
			$term_object  = get_term( $term_id, $term_object->taxonomy);
		}
				
		if ( ! is_wp_error( $term_object ) ) {
			return $term_object;
		}
	}

	public function get_user_related_posts( $post_type, $user_object) {
		
		$post_field  = $this->get_settings( 'post_field' );

		$author_posts =  get_posts(array(
			'post_type' => $post_type,
			'author' => $user_object->ID,
			'posts_per_page' => -1
		));

		if( !empty( $author_posts ) ) {
			$posts_array = $this->get_post_navtive_field($post_field, $author_posts, true);

			if( !empty( $posts_array ) ) {
				return $this->get_rendered_output($posts_array);
			}
		}
	}

	public function get_user_data_value($user_field, $user_object, $user_meta_key = '') {
		$output = '';
		$is_meta_object  = $this->get_settings( 'is_meta_object' );
		$is_meta_image  = $this->get_settings( 'is_meta_image' );

		if(empty($user_object)) {
			return;
		}

		if($is_meta_object == 'yes') {
			
			$field_data = get_user_meta($user_object->ID, $user_meta_key, true);
			$output = $this->get_object_meta_values($field_data);
			
		} else {
			if( $user_field == 'user_meta' ) {
				$meta_value = $this->get_user_metadata_value( $user_object, $user_meta_key );

				if($is_meta_image == 'yes') {
					$output = $this->get_image_url($meta_value);
				} else {
					if ( ! empty( $meta_value ) ) {
						$output = $meta_value;
					}
				}
				
			} elseif( $user_field == 'related_posts' ) {
				$post_type    = $this->get_settings( 'post_type' );
				$output = $this->get_user_related_posts( $post_type, $user_object );
			} else {
				$output =  $this->get_user_navtive_field( $user_field, $user_object );
			}
		}

		return $output;
	}

	public function get_post_data_value($post_meta_key) {
		$output = '';
		if(empty( $post_meta_key )) {
			return;
		}

		$is_meta_object  = $this->get_settings( 'is_meta_object' );
		$is_meta_image  = $this->get_settings( 'is_meta_image' );
		$post_id = get_the_ID();
		$post_meta = get_post_meta( $post_id, $post_meta_key, true );

		if($is_meta_object == 'yes') {
			$output = $this->get_object_meta_values($post_meta);
		} else {
		
			if($is_meta_image == 'yes') {
				$output = $this->get_image_url($post_meta);
			} else {
				if ( ! empty( $post_meta ) ) {
					$output =  $post_meta;
				}
			}
		}

		return $output;
	}

	public function get_term_data_value($term_object, $term_meta_key) {
		$is_meta_object  = $this->get_settings( 'is_meta_object' );
		$is_meta_image  = $this->get_settings( 'is_meta_image' );
		
		$output = '';
		if( empty( $term_meta_key ) ) {
			return;
		}

		$is_meta_object  = $this->get_settings( 'is_meta_object' );
		$term_value = get_term_meta( $term_object->term_id, $term_meta_key, true );

		if($is_meta_object == 'yes') {
			$output = $this->get_object_meta_values($term_value);
		} else {

			if($is_meta_image == 'yes') {
				$output = $this->get_image_url($term_value);
			} else {
				if( $term_value ) {
					$output = $term_value;
				}
			}
		}

		return $output;
	}

	public function get_image_url($value) {

		if ( $value && is_numeric( $value ) ) {
			$value = wp_get_attachment_image_url( $value, 'full', false );
		} elseif( is_array($value)) {
			$value = $value['url'];
		} 

		return $value;
	}
	
	// public function get_related_term_data_value($term_object, $term_meta_key) {
	// 	if( empty( $term_meta_key ) ) {
	// 		return;
	// 	}

	// 	$is_meta_object  = $this->get_settings( 'is_meta_object' );
	// 	$term_value = get_term_meta( $term_object->term_id, $term_meta_key, true );

	// 	if($is_meta_object == 'yes') {
	// 		$this->get_object_meta_values($term_value);
	// 	} else {
	// 		if( $term_value ) {
	// 			echo $term_value;
	// 		}
	// 	}
	// }

	public function get_object_meta_values($field_data) {

		if(empty($field_data)) {
			return;
		}
	
		$object_relation  = $this->get_settings( 'object_relation' );

		if(is_array($field_data)) {
			if($object_relation == 'term') {
				$meta_field_context  = $this->get_settings( 'meta_term_field' );

				if(!empty($field_data)) {
					$term_value = array();

					foreach( $field_data as $term_id ) {
						$term_object[] = get_term($term_id);
					}

					if(!empty($term_object)) {
						foreach( $term_object as $term ) {
							$term_value[] = $this->get_term_native_field( $term, $meta_field_context );
						}
						return $this->get_rendered_output($term_value);
					}
				}
			} else if($object_relation == 'user') {
				$user_value = array();
				$meta_field_context  = $this->get_settings( 'meta_user_field' );

				if(!empty($field_data)) {
					foreach( $field_data as $user_id ) {
						$user_object[]  = get_user_by('id', $user_id);
					}

					if(!empty($user_object)) {
						foreach( $user_object as $user ) {
							$user_value[] =  $this->get_user_navtive_field( $meta_field_context, $user );
						}
						return $this->get_rendered_output($user_value);
					}
				}
			} else if($object_relation == 'post') {
				
				$meta_field_context  = $this->get_settings( 'meta_post_field' );
				
				if(!empty($field_data)) {
					foreach( $field_data as $post_id ) {
						$post_object[]  = get_post($post_id);
					}

					if(!empty($post_object)) {
						$post_value =  $this->get_post_navtive_field( $meta_field_context,$post_object, true );
						return $this->get_rendered_output($post_value);
					}
				}

			}
		}
	}

	public function get_level_value($data, $key) {
		
		if(is_object($data)) {
			
			if(isset($data->$key) && !empty($data->$key)) {
				$value = $data->$key;
			}
		} else if(is_array($data)) {
			
			if(isset($data[$key]) && !empty($data[$key])) {
				$value = $data[$key];
			}
		}
 
		return $value;
	}

	public function render() {
		$context = $this->get_settings( 'tag_context' );
		$select_taxonomy = $this->get_settings( 'select_taxonomy' );
		$show_parent = $this->get_settings( 'show_parent' );
		/* meta option */
		$user_field    = $this->get_settings( 'user_field' );
		$term_field    = $this->get_settings( 'term_field' );
		$post_field    = $this->get_settings( 'post_field' );
		/* meta keys */
		$user_meta_key    = $this->get_settings( 'user_meta_key' );
		$term_meta_key    = $this->get_settings( 'term_meta_key' );
		$post_meta_key    = $this->get_settings( 'post_meta_key' );
		/* specific term */
		$tax_specific_term = $this->get_settings( 'context_tax_specific_term' );
		$tax_term_id = $this->get_settings( 'context_tax_term_id' );

		$query_id = $this->get_settings( 'jet_query_id' );

		if ( empty( $context ) && function_exists('jet_engine')) {
			return;
		}

		switch ( $context ) {

			case 'current_term':				
				$term_object = get_queried_object();

				if( isset($term_object->taxonomy) ) {

					if( $show_parent == 'yes' ) {
						$term_object = $this->get_parent_term( $term_object );
					}

					if( $term_field != 'term_meta' ) {
						echo $this->get_term_native_field( $term_object, $term_field );
					} else {
						
						if( empty( $term_meta_key )) {
							return;
						}
						
						echo $this->get_term_data_value($term_object, $term_meta_key);
					}	
				}
				break;

			case 'related_term':		

				if( empty( $select_taxonomy ) ) {
					return;
				}

				if($tax_specific_term == 'yes') {
					$terms = get_terms( $select_taxonomy, 
						array(
							'parent' => $tax_term_id , 
							'depth'=> 1
						)
					);
				} else {
					$terms = wp_get_object_terms( get_the_ID(),  $select_taxonomy );
				}

				if ( ! empty( $terms ) ) {
					// if ( current_user_can( 'administrator' ) ) {
					// 	echo '<pre>'; print_r($terms); echo '</pre>';
					// }

					if ( ! is_wp_error( $terms ) ) {
						
						foreach( $terms as $term_object ) {
							
							if( $show_parent == 'yes' ) {
								$term_object = $this->get_parent_term( $term_object );
							}

							if( $term_field != 'term_meta' ) {
								$term_value = $this->get_term_native_field( $term_object, $term_field );
								
								if( !empty( $term_value ) ) {
									$term_values[] = $term_value;
								}
							} else {

								if( empty( $term_meta_key ) ) {
									return;
								}
								// $term_value =  get_term_meta( $term_object->term_id, $term_meta_key, true );
								
								$term_value = $this->get_term_data_value($term_object, $term_meta_key);
								if( !empty( $term_value ) ) {
									$term_values[] = $term_value;
								}
							}
						}
						
						if( $show_parent == 'yes' ) {
							$term_values = array_unique( $term_values );
						}
						echo $this->get_rendered_output($term_values);
					}
				}

				break;
	
			case 'current_user':
				$user_object = wp_get_current_user();
				echo $this->get_user_data_value($user_field, $user_object, $user_meta_key);
				break;

			case 'current_author':
				if(function_exists('jet_engine')) {
					$user_object = jet_engine()->listings->data->get_current_author_object();
				} else {
					$post_id =  get_the_ID();
					$author_id = get_post_field( 'post_author', $post_id );

					if($author_id) {
						$user_object = get_user_by('id', $author_id);
					}

				}
 				echo $this->get_user_data_value($user_field, $user_object, $user_meta_key);
				break;

			case 'queried_user_author':
				$post_id = get_the_ID();
				$post = get_post( $post_id );

				if ( $post ) {
					$user_id = get_the_author_meta( 'ID', $post->post_author );
				}

				if ( $user_id ) {
					$user_object = get_user_by( 'ID', $user_id );
					echo $this->get_user_data_value($user_field, $user_object, $user_meta_key);
				}
				break;

			case 'queried_user':
				if(function_exists('jet_engine')) {
					$user_object = jet_engine()->listings->data->get_queried_user_object();
					$user_object = apply_filters( 'jet-engine/elementor/dynamic-tags/user-context-object/' . $context, $user_object );
					echo $this->get_user_data_value($user_field, $user_object, $user_meta_key);

				}  
				break;
			case 'displayed_user':
				if(function_exists('buddypress')) {
					$bp = buddypress();
					$id = ! empty( $bp->displayed_user->id )? $bp->displayed_user->id: 0;

					$user = get_user_by('ID', $id);
					$user_object = $user->data;
					echo $this->get_user_data_value($user_field, $user_object, $user_meta_key);
				}  
				break;
			case 'current_post':
				if( $post_field != 'post_meta' ) {
					echo $this->get_post_navtive_field( $post_field );
				} else {
					if( empty( $post_meta_key )) {
						return;
					}

					echo $this->get_post_data_value($post_meta_key);
				}
				break;
			
			case 'queried_post_author':
				if(function_exists('jet_engine')) {
					$object = jet_engine()->listings->data->get_current_object();

					if( !empty( $object ) ) {
						$user_id = get_the_author_meta( 'ID', $object->post_author );
	
						if ( $user_id ) {
							$user_object = get_user_by( 'ID', $user_id );     
							echo $this->get_user_data_value($user_field, $user_object, $user_meta_key);
						}
					}

				} else {
					$post_id =  get_the_ID();
					$author_id = get_post_field( 'post_author', $post_id );

					if($author_id) {
						$user_object = get_user_by('id', $author_id);
						echo $this->get_user_data_value($user_field, $user_object, $user_meta_key);
					}
				}
				 
				break;
			case 'jet_query_builder':

				if(function_exists('jet_engine') && !empty($query_id)) {

					$query = Manager::instance()->get_query_by_id( $query_id );

					if(!empty($query)) {
						
						$object = $query->get_items();
						$query_data = array();
						$settings = $this->get_settings_for_display();
	
						$image_return_type = $settings['image_return_type'];
	
						$jet_array_level = $settings['jet_array_level'];
						$level_1 = $settings['jet_query_level_1_key'];
						$level_2 = $settings['jet_query_level_2_key'];
						$level_3 = $settings['jet_query_level_3_key'];
	
						// echo '<pre>'; print_r($settings); echo '</pre>';
						
						if( !empty( $object ) ) {
							foreach( $object as $item ) {
	
								if( $jet_array_level == 'level_1' || $jet_array_level == 'level_2' || $jet_array_level == 'level_3' ) {
									$value = $this->get_level_value($item, $level_1);
								} 
								
								if( $jet_array_level == 'level_2' || $jet_array_level == 'level_3' ) {
									$value = $this->get_level_value($value, $level_2);
								} 
								
								if( $jet_array_level == 'level_3' ) {
									$value = $this->get_level_value($value, $level_3);
								}
								
		
								if(!empty($value) && !is_array($value) && !is_object($value)) {
									$query_data[] = $value;
								}
							}
						}
		
						echo $this->get_rendered_output($query_data);
					}
				}
				
				break;
		}

	}
	
}