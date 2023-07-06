<?php

namespace Gloo\Modules\Form_Fields_For_Terms\Fields;

use \ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Terms_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	public $depended_styles = [ 'gloo-for-elementor' ];
	public $depended_scripts = [ 'gloo_form_fields_for_terms' ];

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

		wp_register_script( 'gloo_form_fields_for_terms', gloo()->plugin_url( 'includes/modules/form-fields-for-terms/assets/js/script.js'), array('jquery'), '1.0');
	}

	public function get_name() {
		return 'Terms Field';
	}

	public function get_label() {
		return __( 'Terms Field', 'gloo' );
	}

	public function get_type() {
		return 'gloo_terms_field';
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


		$field_controls = [
			'gloo_term_fields_output'    => [
				'name'         => 'gloo_term_fields_output',
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
			'gloo_term_fields_output_label_type'    => [
				'name'         => 'gloo_term_fields_output_label_type',
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
				// 'description'  => __( 'Select the label data type you desire for the Terms.', 'gloo' ),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_fields_output_label_type_key'    => [
				'name'         => 'gloo_term_fields_output_label_type_key',
				'label'        => __( 'Meta Key for input label', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'condition'    => [
					'field_type' => $this->get_type(),
					'gloo_term_fields_output_label_type' => 'custom_meta_field',
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_fields_output_value_type'    => [
				'name'         => 'gloo_term_fields_output_value_type',
				'label'        => __( 'Return Value', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'default' => 'id',
				'options'      => [
					'id'    => 'ID',
					'title'   => 'Title',
					'slug' => 'Slug',
					'custom_meta_field' => "Custom Meta Field"
				],
				// 'description'  => __( 'Select the label data type you desire for the Terms.', 'gloo' ),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_fields_output_value_type_key'    => [
				'name'         => 'gloo_term_fields_output_value_type_key',
				'label'        => __( 'Meta Key for return value', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'condition'    => [
					'field_type' => $this->get_type(),
					'gloo_term_fields_output_value_type' => 'custom_meta_field',
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_fields_query'     => [
				'name'         => 'gloo_term_fields_query',
				'label'        => __( 'Query', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'options'      => [
					'by_tax' => 'By Taxonomy',
					'manual' => 'Manual Selection',
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_fields_by_tax'    => [
				'name'         => 'gloo_term_fields_by_tax',
				'label'        => __( 'Taxonomy', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT2,
				'condition'    => [
					'field_type'             => $this->get_type(),
 				],
				'options'      => $options,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_fields_selection' => [
				'name'         => 'gloo_term_fields_selection',
				'label'        => __( 'Select Terms', 'gloo' ),
				'type'         => Query_Module::QUERY_CONTROL_ID,
				'options'      => [],
				'label_block'  => true,
				'multiple'     => true,
				'autocomplete' => [
					'object'  => Query_Module::QUERY_OBJECT_TAX,
					'display' => 'detailed',
				],
				'condition'    => [
					'field_type'              => $this->get_type(),
					'gloo_term_fields_query!' => 'by_tax'

				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],

			'gloo_term_fields_exclude' => [
				'name'         => 'gloo_term_fields_exclude',
				'label'        => __( 'Exclude Terms', 'gloo' ),
				'type'         => Query_Module::QUERY_CONTROL_ID,
				'options'      => [],
				'label_block'  => true,
				'multiple'     => true,
				'autocomplete' => [
					'object'  => Query_Module::QUERY_OBJECT_TAX,
					'display' => 'detailed',
				],
				'condition'    => [
					'field_type'              => $this->get_type(),
					'gloo_term_fields_query!' => 'manual'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_fields_depth'   => [
				'name'         => 'gloo_term_fields_depth',
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
			'gloo_term_allow_multiple' => [
				'name'         => 'gloo_term_allow_multiple',
				'label' => esc_html__( 'Multiple Selecton', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo' ),
				'label_off' => esc_html__( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
					'gloo_term_fields_output' => 'select'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_select_placeholder' => [
				'name'         => 'gloo_term_select_placeholder',
				'label' => esc_html__( 'Place Holder', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo' ),
				'label_off' => esc_html__( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'    => [
					'field_type' => $this->get_type(),
					'gloo_term_fields_output' => 'select'
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_term_select_placeholder_text' => [
				'name'         => 'gloo_term_select_placeholder_text',
				'label' => esc_html__( 'Place Holder Text', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'condition'    => [
					'field_type' => $this->get_type(),
					'gloo_term_fields_output' => 'select',
					'gloo_term_select_placeholder' => 'yes',
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
		];

		$control_data['fields']['inline_list']['conditions']['terms'][0]['value'][] = $this->get_type();
		$control_data['fields']['inline_list']['conditions']['terms'][]             = [
			'name'     => 'gloo_term_fields_output',
			'value'    => [ 'checkbox', 'radio' ],
			'operator' => 'in'
		];
		//echo '<pre>'; print_r($control_data); echo '</pre>';
		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}


	public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			'gloo_term_fields_style',
			[
				'label' => __( 'Term Fields', 'gloo' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'gloo_term_fields_style_checkbox_heading',
			[
				'label'     => __( 'Checkbox & Radio', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'

			]
		);

		$element->add_control(
			'gloo_term_fields_style_checkbox_color',
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
				'name'     => 'gloo_term_fields_style_checkbox_text',
				'label'    => __( 'Typography', 'gloo' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-field-option',
			]
		);

		$element->add_control(
			'gloo_term_fields_style_checkbox_child_color',
			[
				'label'     => __( 'Child Terms Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-option.gloo-child-term label' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'gloo_term_fields_style_checkbox_child_text',
				'label'    => __( 'Child Terms Typography', 'gloo' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-field-option.gloo-child-term',
			]
		);

		$element->add_control(
			'gloo_term_fields_style_indent',
			[
				'label'   => __( 'Indent Child Terms', 'gloo' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',

			]
		);

		$element->add_responsive_control(
			'gloo_term_fields_style_indent_width',
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
					'gloo_term_fields_style_indent' => 'yes'
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-field-subgroup:not(.elementor-subgroup-inline) .elementor-field-option.gloo-child-term'                   => 'margin: 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-field-subgroup:not(.elementor-subgroup-inline) .elementor-field-option.gloo-child-term.gloo-term-depth-3' => 'margin: 0 calc({{SIZE}}{{UNIT}} * 2);',
				],
			]
		);


		$element->end_controls_section();
	}
	
	public function get_term_formated_value($term_item, $return_type, $settings , $type, $item_index) {
				
		if($return_type == 'title') {
			$value = $term_item->name;
		} elseif($return_type == 'slug') {
			$value = $term_item->slug;
		} elseif($return_type == 'custom_meta_field') {
				
			if($type == 'label') {
				$meta_key = $settings[ $item_index ]['gloo_term_fields_output_label_type_key'];
			} else {
				$meta_key = $settings[ $item_index ]['gloo_term_fields_output_value_type_key'];
			}

			$value = get_term_meta($term_item->term_id, $meta_key, true);
 		} else {
			$value = $term_item->term_id;
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

		$output_type = $settings[ $item_index ]['gloo_term_fields_output'];
		$query       = $settings[ $item_index ]['gloo_term_fields_query'];
		$exclude     = $settings[ $item_index ]['gloo_term_fields_exclude'];
		$selection   = $settings[ $item_index ]['gloo_term_fields_selection'];
		$depth       = $settings[ $item_index ]['gloo_term_fields_depth'];
		$allow_multiple = $settings[ $item_index ]['gloo_term_allow_multiple'];

		$output_label_type = $settings[ $item_index ]['gloo_term_fields_output_label_type'];
		$output_label_type_key = $settings[ $item_index ]['gloo_term_fields_output_label_type_key'];

		$return_value_type = $settings[ $item_index ]['gloo_term_fields_output_value_type'];
		$return_value_type_key = $settings[ $item_index ]['gloo_term_fields_output_value_type_key'];
		

		$terms = $options = $child_terms = [];
		switch ( $query ) {

			case 'by_tax' :
				$terms = get_terms( array(
					'taxonomy'   => $settings[ $item_index ]['gloo_term_fields_by_tax'],
					'hide_empty' => false,
					'orderby'    => 'parent',
					'exclude'    => $exclude,
				) );
				break;
			case 'manual' :
				$terms = get_terms( array(
					'include'    => $selection,
					'hide_empty' => false,
					'exclude'    => $exclude,
					'orderby'    => 'parent',
				) );
				break;
		}

		if ( ! empty( $terms ) ) {


			foreach ( $terms as $term ) {

				$option_label = $this->get_term_formated_value($term, $output_label_type, $settings, 'label', $item_index);
				$option_value = $this->get_term_formated_value($term, $return_value_type, $settings, 'value', $item_index);
  
				$ancestors       = get_ancestors( $term->term_id, $term->taxonomy );

			//	echo '<pre>'; print_r($ancestors); echo '</pre>';
				$term->ancestors = $ancestors; // array( 0 => 15, 1 => 45 ) - 3rd level term
				$term->depth     = count( $ancestors ) + 1;

				if ( $depth != 0 && $term->depth > $depth ) {
					continue;
				}

				if ( $term->parent != 0 ) {

					$child_option_label = $this->get_term_formated_value($term,$output_label_type, $settings, 'label', $item_index);
					$child_option_value = $this->get_term_formated_value($term,$return_value_type, $settings, 'value', $item_index);
	
					$child_terms[ $child_option_value ] = $child_option_label;
				}

				if ( $term->depth > 1 ) {
					$depth_option_label = $this->get_term_formated_value($term,$output_label_type, $settings, 'label', $item_index);
					$depth_option_value = $this->get_term_formated_value($term,$return_value_type, $settings, 'value', $item_index);

					$term_parent = get_term_by( 'id', $term->parent, $term->taxonomy ); 
 					$parent_term_value = $this->get_term_formated_value($term_parent, $return_value_type, $settings, 'value', $item_index);
					 
					$options = $this->array_insert_after( $options, $parent_term_value, [ $depth_option_value => $depth_option_label ] );
					continue;
				}
 
				
				$options[ $option_value ] = $option_label;

			}
 
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
		$child_terms = $item['child_terms'];

		$options     = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );
		$html        = '';
		$parent_open = false;

		if ( $options ) {

			if(isset($item['required']) && $item['required'] == true)
			  $html .= '<input type="text" class="gloo_required_message_input" name="gloo_'.$form->get_attribute_id( $item ).'" required />';

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
							isset( $child_terms[ intval( $option_value ) ] ) ? 'gloo-child-term' : 'gloo-parent-term',
							$child_terms[ intval( $option_value ) ] > 2 ? 'gloo-term-depth-' . $child_terms[ intval( $option_value ) ] : '',
						],
						'data-term-name' => [ trim( $option_label ) ]
					]
				);

				if ( ( ! empty( $item['field_value'] ) && $option_value === $item['field_value'] ) || ( isset( $item['gloo_checked_terms'] ) && in_array( $option_value, $item['gloo_checked_terms'] ) ) ) {
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
					$html        .= '<div class="gloo-term-group">';
					$parent_open = true;
				}

				$html .= '<span ' . $form->get_render_attribute_string( $element_id . 'wrapper' ) . '><input ' . $form->get_render_attribute_string( $element_id ) . '> <label for="' . $html_id . '">' . $option_label . '</label></span>';


			}
			if ( $parent_open ) {
				$html .= '</div>';
			}
			$html .= '</div>';
		}

		return $html;
	}

	public function make_select_field( $item, $i, $form ) {

		$settings = $form->get_settings_for_display( 'form_fields' );
		$is_placeholder = $settings[ $i ]['gloo_term_select_placeholder'];
		$placeholder_text = $settings[ $i ]['gloo_term_select_placeholder_text'];

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

		if(empty($item['field_options']))
			return '';
		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );

		if ( ! $options ) {
			return '';
		}

		ob_start();
		?>
        <div <?php echo $form->get_render_attribute_string( 'select-wrapper' . $i ); ?>>
            <select <?php echo $form->get_render_attribute_string( 'select' . $i ); ?>>
				<?php
				if($is_placeholder == 'yes' && !empty($placeholder_text)){
					echo '<option ' . $form->get_render_attribute_string( '' ) . '> '.$placeholder_text.' </option>';
				}
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
					if ( ( ! empty( $item['field_value'] ) && in_array( $option_value, explode( ',', $item['field_value'] ) ) ) || ( isset( $item['gloo_checked_terms'] ) && in_array( $option_value, $item['gloo_checked_terms'] ) ) ) {
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