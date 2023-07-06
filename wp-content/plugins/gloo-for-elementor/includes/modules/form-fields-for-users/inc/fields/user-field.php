<?php

namespace Gloo\Modules\Form_Fields_For_Users\Fields;

use \ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Users_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	public $depended_styles = [ 'gloo-for-elementor' ];
	public $depended_scripts = [ 'gloo_form_fields_for_users' ];
	private $prefix = 'gloo_user_';
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

		wp_register_script( 'gloo_form_fields_for_users', gloo()->plugin_url( 'includes/modules/form-fields-for-users/assets/js/script.js'), array('jquery'), '1.0');
	}

	public function get_name() {
		return 'User Types';
	}

	public function get_label() {
		return __( 'User Types', 'gloo' );
	}

	public function get_type() {
		return 'gloo_user_field';
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
 
		global $wp_roles;
		$roles = $wp_roles->get_names();
		
		/* users order by */
		$order_by = [
 			'ID' => 'ID',
			'display_name' => 'Display Name',
			'user_name' => 'Username',
			'user_registered' => 'User Registered',
			'post_count' => 'Post Count',
			'email' => 'Email'
		];

		$order = [
			'asc' => 'Asc',
			'desc' => 'Desc'
		];
		 
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
				'description'  => __( 'Select the output you desire for the Users.', 'gloo' ),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'fields_user_role'     => [
				'name'         => $this->prefix.'fields_user_role',
				'label'        => __( 'User Role', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT2,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'options'      => $roles,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'user_order_by'   => [
				'name'         => $this->prefix.'user_order_by',
				'label'        => __( 'Order By', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'default'      => 'ID',
				'options'      => $order_by,
 				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			$this->prefix.'user_order'   => [
				'name'         => $this->prefix.'user_order',
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
			$this->prefix.'users_exclude_by_id'    => [
				'name'         => $this->prefix.'users_exclude_by_id',
				'label'        => __( 'Exclude Specific Users By Id', 'gloo' ),
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
			$this->prefix.'users_include_by_id'    => [
				'name'         => $this->prefix.'users_include_by_id',
				'label'        => __( 'Include Specific Users By Id', 'gloo' ),
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


		// $control_data['fields']['allow_multiple']['conditions']['terms'][0]['operator'] = 'in';
		// $control_data['fields']['allow_multiple']['conditions']['terms'][0]['value']    = [
		// 	$this->get_type()
		// ];
		// $control_data['fields']['allow_multiple']['conditions']['terms'][]              = [
		// 	'name'  => $this->prefix.'fields_output',
		// 	'value' => 'select'
		// ];


		// $control_data['fields']['select_size']['conditions']['terms'][0]['operator'] = 'in';
		// $control_data['fields']['select_size']['conditions']['terms'][0]['value']    = [
		// 	'select',
		// 	$this->get_type()
		// ];
		// $control_data['fields']['select_size']['conditions']['terms'][]              = [
		// 	'name'  => $this->prefix.'fields_output',
		// 	'value' => 'select',
		// ];


		// $control_data['fields']['inline_list']['conditions']['terms'][0]['value'][] = $this->get_type();
		// $control_data['fields']['inline_list']['conditions']['terms'][]             = [
		// 	'name'     => $this->prefix.'fields_output',
		// 	'value'    => [ 'checkbox', 'radio' ],
		// 	'operator' => 'in'
		// ];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}


	public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			$this->prefix.'fields_style',
			[
				'label' => __( 'User Types Fields', 'gloo' ),
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


	/**
	 * @param      $item
	 * @param      $item_index
	 * @param Form $form
	 */
	public function render( $item, $item_index, $form ) {  

		$settings = $form->get_settings_for_display( 'form_fields' );

		$output_type = $settings[ $item_index ][$this->prefix.'fields_output'];
		$user_role       = $settings[ $item_index ][$this->prefix.'fields_user_role'];
		$users_exclude     = $settings[ $item_index ][$this->prefix.'users_exclude_by_id'];
		$users_include     = $settings[ $item_index ][$this->prefix.'users_include_by_id'];
		$user_order_by = $settings[ $item_index ][$this->prefix.'user_order_by'];
		$user_order = $settings[ $item_index ][$this->prefix.'user_order'];
		$allow_multiple = $settings[ $item_index ][$this->prefix.'allow_multiple'];
		$args = array();

		if(!empty($user_role)) {
			$args['role'] = $user_role;
		}

		/* posts order */
		if(!empty($user_order_by) || !empty($user_order)) {

			if(!empty($user_order_by)) {
				$args['orderby'] = $user_order_by;
			}

			if(!empty($user_order)) {
				$args['order'] = $user_order;
			}
		}

		/* posts exclude */
		if(!empty($users_exclude)) {
			$user_exclude_ids = explode(',', $users_exclude);

			if(is_array($user_exclude_ids)) {
				$args['exclude'] = $user_exclude_ids;
			}
		}
 
		/* posts exclude */
		if(!empty($users_include)) {
			$user_include_ids = explode(',', $users_include);

			if(is_array($user_include_ids)) {
				$args['include'] = $user_include_ids;
			}
		}
		
		//print_r($args);

		$user_query = new \WP_User_Query($args);
		$options = array();

		if ( ! empty( $user_query->get_results() ) ) {
			foreach ( $user_query->get_results() as $user ) {
				$options[$user->data->ID] = $user->display_name;
			}
		}		
  
		if($output_type == 'select' && is_array($options)) {
			$new_options = array('' => __('--Select--', 'gloo_for_elementor'));
			
			foreach($options as $key=>$value){
				$new_options[$key] = $value;
			}

			$options = $new_options;
		}
		
		$item['field_options'] = $this->array_to_options( $options );
		$item['field_type']    = $output_type;

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
 
				if ( ( ! empty( $item['field_value'] ) && $option_value === $item['field_value'] ) || ( isset( $item['gloo_checked_user_types'] ) && in_array( $option_value, $item['gloo_checked_user_types'] ) ) ) {
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
					if ( ( ! empty( $item['field_value'] ) && in_array( $option_value, explode( ',', $item['field_value'] ) ) ) || ( isset( $item['gloo_checked_user_types'] ) && in_array( $option_value, $item['gloo_checked_user_types'] ) ) ) {
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