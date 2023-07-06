<?php

namespace Gloo\Modules\Form_Fields_For_CPT\Fields;

use \ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class CPT_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	public $depended_styles = [ 'gloo-for-elementor' ];
	public $depended_scripts = [ 'gloo_form_fields_for_cpt' ];
	private $prefix = 'gloo_cpt_';
	public function __construct() {

		add_action( 'elementor/element/form/section_form_style/after_section_end', [
			$this,
			'add_control_section_to_form'
		], 10, 2 );


		add_action( 'elementor/widget/print_template', function ( $template, $widget ) {
			if ( 'form' === $widget->get_name() ) {
				$template = false;
			}

			return $template;
		}, 10, 2 );

		parent::__construct();

		wp_register_script( 'gloo_form_fields_for_cpt', gloo()->plugin_url( 'includes/modules/form-fields-for-cpt/assets/js/script.js'), array('jquery'), '1.0');
	}

	public function get_name() {
		return 'Post Types';
	}

	public function get_label() {
		return __( 'Post Types', 'gloo' );
	}

	public function get_type() {
		return 'gloo_cpt_field';
	}

	/**
	 * @param Widget_Base $widget
	 */
	public function update_controls( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$taxonomies = get_taxonomies(
			[]
			, 'objects'
		);

		$options    = [ '' => '' ];
		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		/* post types options */
		$args = array(
			'public'   => true,
		);

		$post_types = array();
		$types = get_post_types( $args, 'objects' );
		
		if(!empty($types)) {
			foreach ( $types as $type ) {
				$post_types[ $type->name ] = $type->label;
			}
		}

		/* posts order by */
		$order_by = [
			'date' => 'Date',
			'ID' => 'ID',
			'menu_order' => 'Menu Order',
			'title' => 'Alphabetical'
		];
		$order = [
			'asc' => 'Asc',
			'desc' => 'Desc'
		];
		
		// echo '<pre>';
		// print_r($post_types);
		// echo '</pre>';

		$field_controls = [
			$this->prefix.'fields_output'    => [
				'name'         => $this->prefix.'fields_output',
				'label'        => __( 'Output', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'options'      => [
					'select'   => 'Select',
					'radio'    => 'Radio',
					'checkbox' => 'Checkbox',
				],
				'description'  => __( 'Select the output you desire for the Terms.', 'gloo' ),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'fields_output_data_type'    => [
				'name'         => $this->prefix.'fields_output_data_type',
				'label'        => __( 'Input Label', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'default' => 'title',
				'options'      => [
					'title'   => 'Title',
					'id'    => 'ID',
					'slug' => 'Link',
					'custom_meta_field' => "Custom Meta Field"
				],
				'description'  => __( 'Select the output data type you desire for the Posts.', 'gloo' ),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'fields_output_data_type_key'    => [
				'name'         => $this->prefix.'fields_output_data_type_key',
				'label'        => __( 'Meta key for input label', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'fields_output_data_type' => 'custom_meta_field',
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'fields_input_return_value'    => [
				'name'         => $this->prefix.'fields_input_return_value',
				'label'        => __( 'Return Value', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'default' => 'id',
				'options'      => [
					'title'   => 'Title',
					'id'    => 'ID',
					'slug' => 'Link',
					'custom_meta_field' => "Custom Meta Field"
				],
				// 'description'  => __( 'Select the label data type you desire for the Terms.', 'gloo' ),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'fields_return_value_meta_key'    => [
				'name'         => $this->prefix.'fields_return_value_meta_key',
				'label'        => __( 'Meta Key for return value', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'fields_input_return_value' => 'custom_meta_field',
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'fields_post_type'     => [
				'name'         => $this->prefix.'fields_post_type',
				'label'        => __( 'Post Type', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT2,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'options'      => $post_types,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'post_order_by'   => [
				'name'         => $this->prefix.'post_order_by',
				'label'        => __( 'Order By', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'default'      => 'date',
				'options'      => $order_by,
 				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'post_order'   => [
				'name'         => $this->prefix.'post_order',
				'label'        => __( 'Order', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'default'      => 'desc',
				'options'      => $order,
 				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'posts_exclude_by_id'    => [
				'name'         => $this->prefix.'posts_exclude_by_id',
				'label'        => __( 'Exclude Specific Posts By Id', 'gloo' ),
				'label_block' => true,
				'type'         => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
 				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'posts_include_by_id'    => [
				'name'         => $this->prefix.'posts_include_by_id',
				'label'        => __( 'Include Specific Posts By Id', 'gloo' ),
				'label_block' => true,
				'type'         => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
 				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'posts_by_tax'    => [
				'name'         => $this->prefix.'posts_by_tax',
				'label'        => __( 'Taxonomy', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT2,
				'condition'    => [
				'field_type'   => $this->get_type(),
 				],
				'options'      => $options,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'exclude_terms'    => [
				'name'         => $this->prefix.'exclude_terms',
				'label'        => __( 'Exclude Terms', 'gloo' ),
				'label_block' => true,
				'type'         => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
 				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'include_terms'    => [
				'name'         => $this->prefix.'include_terms',
				'label'        => __( 'Include Terms', 'gloo' ),
				'label_block' => true,
				'type'         => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'condition'    => [
					'field_type' => $this->get_type(),
 				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'fields_depth'   => [
				'name'         => $this->prefix.'fields_depth',
				'label'        => __( 'Depth', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'default'      => 'all',
				'options'      => [
					'0' => 'All',
					'1' => '1',
					'2' => '2',
					'3' => '3',
				],
				'description'  => 'Depth of terms in the hierarchy to show.',
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'allow_multiple' => [
				'name'         => $this->prefix.'allow_multiple',
				'label' => esc_html__( 'Multiple Selecton', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo' ),
				'label_off' => esc_html__( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
					$this->prefix.'fields_output' => 'select'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
		];

		$control_data['fields']['inline_list']['conditions']['terms'][0]['value'][] = $this->get_type();
		$control_data['fields']['inline_list']['conditions']['terms'][]             = [
			'name'     => $this->prefix.'fields_output',
			'value'    => [ 'checkbox', 'radio' ],
			'operator' => 'in'
		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}


	public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			$this->prefix.'fields_style',
			[
				'label' => __( 'Post Types Fields', 'gloo' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			$this->prefix.'fields_style_checkbox_heading',
			[
				'label'     => __( 'Checkbox & Radio', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'

			]
		);

		$element->add_control(
			$this->prefix.'fields_style_checkbox_color',
			[
				'label'     => __( 'Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-option label' => 'color: {{VALUE}};',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => $this->prefix.'fields_style_checkbox_text',
				'label'    => __( 'Typography', 'gloo' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-field-option',
			]
		);

		$element->add_control(
			$this->prefix.'fields_style_checkbox_child_color',
			[
				'label'     => __( 'Child Terms Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-option.gloo-child-post label' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => $this->prefix.'fields_style_checkbox_child_text',
				'label'    => __( 'Child Posts Typography', 'gloo' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-field-option.gloo-child-post',
			]
		);

		$element->add_control(
			$this->prefix.'fields_style_indent',
			[
				'label'   => __( 'Indent Child Post', 'gloo' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',

			]
		);

		$element->add_responsive_control(
			$this->prefix.'fields_style_indent_width',
			[
				'label'      => __( 'Indent Amount', 'gloo' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition'  => [
					$this->prefix.'fields_style_indent' => 'yes'
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-field-subgroup:not(.elementor-subgroup-inline) .elementor-field-option.gloo-child-post'                   => 'margin: 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-subgroup:not(.elementor-subgroup-inline) .elementor-field-option.gloo-child-post.gloo-post-depth-3' => 'margin: 0 calc({{SIZE}}{{UNIT}} * 2);',
				],
			]
		);


		$element->end_controls_section();
	}

	function moveElement(&$array, $a, $b) {
		$out = array_splice($array, $a, 1);
		array_splice($array, $b, 0, $out);
	}

	function get_posts_children($args, $parent_id){
		$children = array();
		// grab the posts children
		$posts = get_posts( $args );
		// now grab the grand children
		foreach( $posts as $child ){
			// recursion!! hurrah
			$gchildren = $this->get_posts_children($args, $child->ID);
			// merge the grand children into the children array
			if( !empty($gchildren) ) {
				$children = array_merge($children, $gchildren);
			}
		}
		// merge in the direct descendants we found earlier
		$children = array_merge($children,$posts);
		return $children;
	}


	public function get_formated_value($post_item, $return_type, $settings , $type, $item_index) {

		

		if($return_type == 'title') {
			$value = $post_item->post_title;
		} elseif($return_type == 'slug') {
			$value = get_permalink($post_item->ID);
		} elseif($return_type == 'custom_meta_field') {

			if($type == 'label') {
				$meta_key = $settings[ $item_index ][$this->prefix.'fields_output_value_type_key'];
			} elseif( $type == 'value') {
				$meta_key = $settings[ $item_index ][$this->prefix.'fields_return_value_meta_key'];
			}

			$value = get_post_meta($post_item->ID, $meta_key, true);
		} else {
			
			$value = $post_item->ID;
		}

		return $value;
	}

	/**
	 * @param      $item
	 * @param      $item_index
	 * @param Form $form
	 */
	public function render( $item, $item_index, $form ) {  

		$settings = $form->get_settings_for_display( 'form_fields' );

		$output_type = $settings[ $item_index ][$this->prefix.'fields_output'];
		
		/* label field option */
		$output_data_type = $settings[ $item_index ][$this->prefix.'fields_output_data_type'];
		$output_data_type_key = $settings[ $item_index ][$this->prefix.'fields_output_data_type_key'];

		/* return value option */
		$return_value = $settings[ $item_index ][$this->prefix.'fields_input_return_value'];	
		$return_value_meta_key = $settings[ $item_index ][$this->prefix.'fields_return_value_meta_key'];
		
		$post_types       = $settings[ $item_index ][$this->prefix.'fields_post_type'];
		$posts_exclude     = $settings[ $item_index ][$this->prefix.'posts_exclude_by_id'];
		$posts_include     = $settings[ $item_index ][$this->prefix.'posts_include_by_id'];
		$posts_by_tax  = $settings[ $item_index ][$this->prefix.'posts_by_tax'];
		$exclude_terms =  $settings[ $item_index ][$this->prefix.'exclude_terms'];
		$include_terms =  $settings[ $item_index ][$this->prefix.'include_terms'];
		$depth       = $settings[ $item_index ][$this->prefix.'fields_depth'];
		$post_order_by = $settings[ $item_index ][$this->prefix.'post_order_by'];
		$post_order = $settings[ $item_index ][$this->prefix.'post_order'];
		$allow_multiple = $settings[ $item_index ][$this->prefix.'allow_multiple'];
		
		$args = [
			'post_type' => $post_types,
			'posts_per_page' => -1,
			'tax_query' => array(),
			'orderby' => 'date',
			'order' => 'desc',
		];
		
		/* posts order */
		if(!empty($post_order_by) || !empty($post_order)) {

			if(!empty($post_order_by)) {
				$args['orderby'] = $post_order_by;
			}

			if(!empty($post_order)) {
				$args['order'] = $post_order;
			}
		}

		/* posts exclude */
		if(!empty($post_ids)) {
			$args['post__not_in'] = $post_ids;
		}

		/* posts exclude */
		if(!empty($posts_exclude)) {
			$post_ids = explode(',', $posts_exclude);

			if(is_array($post_ids)) {
				$args['post__not_in'] = $post_ids;
			}
		}
 
		/* posts exclude */
		if(!empty($posts_include)) {
			$post_inc_ids = explode(',', $posts_include);

			if(is_array($post_inc_ids)) {
				$args['post__in'] = $post_inc_ids;
			}
		}

		/* posts tax query  */
		if(!empty($posts_by_tax)) {
			$tax_args = [
				'taxonomy'   => $posts_by_tax,
				'hide_empty' => false,
			];

			if(!empty($exclude_terms)) {
				$tax_args['exclude'] = $exclude_terms;
			}

			if(!empty($include_terms)) {
				$tax_args['include'] = $include_terms;
			}

			$terms = get_terms( $tax_args );
			$terms_ex = array();

			if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$terms_ex[] = $term->term_id;
				}
			}

			$args['tax_query'][] = [
				'taxonomy' => $posts_by_tax,
				'field' => 'id',
				'terms' => $terms_ex,
				'include_children' => false,
				'operator' => 'IN'
			];
		}
 
		$terms = $options = $child_terms = $all_posts = [];
		$the_query = new \WP_Query( $args );
		
		$item_array = [];	

		foreach( $the_query->posts as $post ) {
		  
			$ancestors  = get_post_ancestors( $post->ID );
			$post_ancestors = $ancestors; // array( 0 => 15, 1 => 45 ) - 3rd level term
		
			$post_depth = count( $ancestors ) + 1;

			if ( $depth != 0 && $post_depth > $depth ) {
				continue;
			}

			if ( $post->post_parent != 0 ) {
				$child_terms[ $post->ID ] = $post_depth;
			}

			if ( $post->post_parent == 0 ) {
				$all_posts[] = $post;
			}
		}
  
		if(!empty($all_posts)) {

			foreach( $all_posts as $post_item ) {

				$option_label = $this->get_formated_value($post_item,$output_data_type, $settings, 'label', $item_index);
				$option_value = $this->get_formated_value($post_item,$return_value, $settings, 'value', $item_index);
				
				// if($output_data_type == 'id')
				// 	$option_label = $post_item->ID;
				// elseif($output_data_type == 'slug')
				// 	$option_label = get_permalink($post_item->ID);
				// elseif($output_data_type == 'custom_meta_field')
				// 	$option_label = get_post_meta($post_item->ID, $output_data_type_key, true);
				// else
				// 	$option_label = $post_item->post_title;


				// if($return_value == 'title')
				// 	$option_value = $term->name;
				// elseif($return_value == 'slug')
				// 	$option_value = $term->slug;
				// elseif($return_value == 'custom_meta_field' && !empty($return_value_meta_key))
				// 	$option_value = get_term_meta($term->term_id, $return_value_meta_key, true);
				// else
				// 	$option_value = $term->term_id;

				// if( !empty($option_value) && !empty($option_label)) {
				// 	$options[ $option_value ] = $option_label;
				// }

				$options[ $option_value ] = $option_label;
				$childrens = get_page_children( $post_item->ID, $the_query->posts);

				if(!empty($childrens)){
					foreach($childrens as $children) {

						$child_option_label = $this->get_formated_value($children,$output_data_type, $settings, 'label', $item_index);
						$child_option_value = $this->get_formated_value($children,$return_value, $settings, 'value', $item_index);

						$options[ $child_option_value ] = $child_option_label;
				   	}
				}
			}
		}

		if($output_type == 'select' && is_array($options)){
			$new_options = array('' => __('--Select--', 'gloo_for_elementor'));
			foreach($options as $key=>$value){
				$new_options[$key] = $value;
			}
			$options = $new_options;
			// if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '103.152.101.211')
			// 	$options = array_merge(array('' => __('--Select--', 'gloo_for_elementor')), $options);
			//  else
			 	// array_unshift($options,  __('--Select--', 'gloo_for_elementor'));
		}
		$item['field_options'] = $this->array_to_options( $options );
		$item['field_type']    = $output_type;
		$item['child_terms']   = $child_terms;

		if(isset($allow_multiple) && $allow_multiple && $allow_multiple == 'yes') {
			$item['allow_multiple']   = true;
		}

		switch ( $output_type ) :
			case 'select':
				echo $this->make_select_field( $item, $item_index, $form );
				break;
			case 'radio':
			case 'checkbox':
				echo $this->make_radio_checkbox_field( $item, $item_index, $output_type, $form );
				break;
		endswitch;

	}


	public function array_insert_after( array $array, $key, array $new ) {
		$keys  = array_keys( $array );
		$index = array_search( $key, $keys );
		$pos   = false === $index ? count( $array ) : $index + 1;

		return array_slice( $array, 0, $pos, true ) + $new + array_slice( $array, $pos, null, true );
	}

	public function array_to_options( $array ) {
		if ( ! $array ) {
			return [];
		}

		return implode( ' 
        ', array_map(
			function ( $v, $k ) {
				return sprintf( "%s|%s", $v, $k );
			},
			$array,
			array_keys( $array )
		) );
	}

	public function make_radio_checkbox_field( $item, $item_index, $type, $form ) {
		
		// added
		// echo '<pre>';
		// print_r($item);
		// echo '</pre>';
		$child_terms = $item['child_terms'];

		$options     = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );
	 
		$html        = '';
		$parent_open = false;

		if ( $options ) {

			if(isset($item['required']) && $item['required'] == true)
			  $html .= '<input type="text" class="gloo_required_message_input" name="gloo_'.$form->get_attribute_id( $item ).'" required />';
			$post_values = [];
			$html .= '<div class="elementor-field-subgroup ' . esc_attr( $item['css_classes'] ) . ' ' . $item['inline_list'] . '">';
			foreach ( $options as $key => $option ) {
 
				$element_id   = $item['custom_id'] . $key;
				$html_id      = $form->get_attribute_id( $item ) . '-' . $key;
				$option_label = $option;
				$option_value = $option;
				if ( false !== strpos( $option, '|' ) ) {  
					list( $option_label, $option_value ) = explode( '|', $option );
				}
				$option_value = trim( $option_value );
				$post_values[] = $option_value;
				$form->add_render_attribute(
					$element_id,
					[
						'type'  => $type,
						'value' => $option_value,
						'id'    => $html_id,
						'name'  => $form->get_attribute_name( $item ) . ( ( 'checkbox' === $type && count( $options ) > 1 ) ? '[]' : '' ),
					]
				);

				$form->add_render_attribute(
					$element_id . 'wrapper',
					[
						'class'          => [
							'elementor-field-option',
							isset( $child_terms[ intval( $option_value ) ] ) ? 'gloo-child-post' : 'gloo-parent-post',
							$child_terms[ intval( $option_value ) ] > 2 ? 'gloo-post-depth-' . $child_terms[ intval( $option_value ) ] : '',
						],
						'data-post-name' => [ trim( $option_label ) ]
					]
				);
				

				if ( ( ! empty( $item['field_value'] ) && $option_value === $item['field_value'] ) || ( isset( $item['gloo_checked_post_types'] ) && in_array( $option_value, $item['gloo_checked_post_types'] ) ) ) {
					$form->add_render_attribute( $element_id, 'checked', 'checked' );
				}

				if ( $item['required'] && method_exists($form, 'add_required_attribute') && is_callable($form, 'add_required_attribute')) {
					$form->add_required_attribute( $element_id );
				}

				if ( ! isset( $child_terms[ intval( $option_value ) ] ) && $parent_open ) {
					$html        .= '</div>';
					$parent_open = false;
				}

				if ( ! isset( $child_terms[ intval( $option_value ) ] ) ) {
					$html        .= '<div class="gloo-post-group">';
					$parent_open = true; 
				}

				$html .= '<span ' . $form->get_render_attribute_string( $element_id . 'wrapper' ) . '><input ' . $form->get_render_attribute_string( $element_id ) . '> <label for="' . $html_id . '">' . $option_label . '</label></span>';


			}
			if ( $parent_open ) {
				$html .= '</div>';
			}
			$html .= '</div>';

			/* this hidden field consist post ids and will help in removing the post on edit action */
			if(!empty($post_values)) { 
				$html .= '<input type="hidden" name="form_fields[gloo_post_types]['.$item['custom_id'].']" value="'.implode(',',  $post_values).'" />'; 
			}
		}

		return $html;
	}

	public function make_select_field( $item, $i, $form ) {
		$form->add_render_attribute(
			[
				'select-wrapper' . $i => [
					'class' => [
						'elementor-field',
						'elementor-select-wrapper',
						esc_attr( $item['css_classes'] ),
					],
				],
				'select' . $i         => [
					'name'  => $form->get_attribute_name( $item ) . ( ! empty( $item['allow_multiple'] ) ? '[]' : '' ),
					'id'    => $form->get_attribute_id( $item ),
					'class' => [
						'elementor-field-textual',
						'elementor-size-' . $item['input_size'],
					],
				],
			]
		);

		if ( $item['required'] && method_exists($form, 'add_required_attribute') && is_callable($form, 'add_required_attribute')) {
			$form->add_required_attribute( 'select' . $i );
		}

		if ( $item['allow_multiple'] ) {
			$form->add_render_attribute( 'select' . $i, 'multiple' );
			if ( ! empty( $item['select_size'] ) ) {
				$form->add_render_attribute( 'select' . $i, 'size', $item['select_size'] );
			}
		}

		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );

		if ( ! $options ) {
			return '';
		}

		ob_start();
		?>
        <div <?php echo $form->get_render_attribute_string( 'select-wrapper' . $i ); ?>>
            <select <?php echo $form->get_render_attribute_string( 'select' . $i ); ?>>
				<?php
				foreach ( $options as $key => $option ) {
					$option_id    = $item['custom_id'] . $key;
					$option_value = esc_attr( $option );
					$option_label = esc_html( $option );

					if ( false !== strpos( $option, '|' ) ) {
						list( $label, $value ) = explode( '|', $option );
						$option_value = esc_attr( $value );
						$option_label = esc_html( $label );
					}
					$option_value = trim( $option_value );
					$form->add_render_attribute( $option_id, 'value', $option_value );

					// Support multiple selected values
					if ( ( ! empty( $item['field_value'] ) && in_array( $option_value, explode( ',', $item['field_value'] ) ) ) || ( isset( $item['gloo_checked_post_types'] ) && in_array( $option_value, $item['gloo_checked_post_types'] ) ) ) {
						$form->add_render_attribute( $option_id, 'selected', 'selected' );
					}
					echo '<option ' . $form->get_render_attribute_string( $option_id ) . '>' . $option_label . '</option>';
				}
				?>
            </select>
        </div>
		<?php

		$select = ob_get_clean();

		return $select;
	}

}