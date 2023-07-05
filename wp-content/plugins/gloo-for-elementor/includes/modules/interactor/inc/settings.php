<?php

namespace Gloo\Modules\Interactor;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use http\Params;

class Settings {

	private $loaded_post_ids = [];

	private $jet_listing_ids = [];

	private $prefix = 'gloo_interactor_';

	private $settings = '';

	private $current_document = '';

	private $options = [];

	private $events = [];

	private $active_functions = [];

	private $undefined_functions = [];

	public function __construct() {

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'editor_scripts' ) );

		// register elementor controls
		add_action( 'elementor/documents/register_controls', [
			$this,
			'add_interactor_section'
		] );


		// add disable js toggle
		add_action( 'elementor/element/before_section_end', [ $this, 'disable_js_toggle' ], 11, 2 );

		// add jet engine support
		add_filter( 'jet-engine/listing/pre-get-item-content', function ( $content, $post, $i, $widget ) {

			$current_listing    = jet_engine()->listings->data->get_listing();
			$current_listing_id = $current_listing->get_main_id();

			if ( ! $current_listing_id ) {
				return;
			}

			$this->jet_listing_ids[ $current_listing->get_main_id() ][] = $post->ID;

		}, 1, 4 );

		// get all loaded templates/post ids
		add_filter( 'elementor/frontend/builder_content_data', [ $this, 'get_loaded_ids' ], 10, 2 );

		// print out js
		add_action( 'wp_footer', [ $this, 'check_loaded_documents' ], 20, 2 );


	}

	public function editor_scripts() {
		$script_abs_path = gloo()->plugin_path('assets/js/admin/e-editor.js');
		wp_enqueue_script( 'gloo-e-editor', gloo()->plugin_url() . 'assets/js/admin/e-editor.js', [ 'jquery' ], $this->get_file_time($script_abs_path));
		wp_enqueue_script( 'gloo-interactor-button-js', gloo()->plugin_url() . 'assets/js/admin/gloo-interactor-button.js', [ 'jquery' ]);
		wp_enqueue_style('gloo-interactor-button-css', gloo()->plugin_url() . 'assets/css/gloo-interactor-button.css',);
	}

	public function get_events() {

		return $this->get_settings_as_options( $this->prefix . 'events', $this->prefix . 'event_title' );
	}

	public function get_triggers() {
		return $this->get_settings_as_options( $this->prefix . 'triggers', $this->prefix . 'trigger_title' );
	}

	public function get_variables() {
		return $this->get_settings_as_options( $this->prefix . 'variables', $this->prefix . 'variable_name' );
	}

	public function get_current_settings() {
		return $this->settings;
	}

	public function set_current_settings( $element ) {
		$control = (array) $element;
		if ( ! isset( $control["\0Elementor\Controls_Stack\0data"]['settings'] ) ) {
			return;
		}
		$this->settings = $control["\0Elementor\Controls_Stack\0data"]['settings'];

		return $this->settings;
	}

	public function get_settings_as_options( $repeater_key, $title_key ) {

		if ( isset( $this->options[ $repeater_key ] ) && ! empty( $this->options[ $repeater_key ] ) ) {
			return $this->options[ $repeater_key ];
		}

		$current_settings = $this->get_current_settings();

		$settings = isset( $current_settings[ $repeater_key ] ) && ! empty( $current_settings[ $repeater_key ] ) ? $current_settings[ $repeater_key ] : false;
		$results  = [];
		if ( $settings ) {

			foreach ( $settings as $key => $setting ) {
				if ( ! isset( $setting['_id'] ) ) {
					continue;
				}
				$title                      = isset( $setting[ $title_key ] ) && $setting[ $title_key ] ? $setting[ $title_key ] : 'Item #' . ( $key + 1 );
				$results[ $setting['_id'] ] = $title;
			}
		}
		$this->options[ $repeater_key ] = $results;

		return $results;
	}

	
	public function get_file_time($file){
				return date("ymd-Gis", filemtime( $file ));
		}
	


	public function disable_js_toggle( $element, $section_id ) {

		if ( $section_id !== 'gloo_interactor_' ) {
			return;
		}

		$element->add_control(
			$this->prefix . 'disable_auto_update',
			[
				'label'        => __( 'Disable Auto Update', 'gloo_for_elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'description'  => 'When JS is disabled you will need to refresh the Elementor editor page to see new items in the action selectors events',
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);

	}

	public function add_interactor_section( $element ) {

		if ( $element->get_name() === 'widget' ) {
			return;
		}

		$this->set_current_settings( $element );

		$element->start_controls_section(
			$this->prefix,
			array(
				'tab'   => Controls_Manager::TAB_ADVANCED,
				'label' => __( 'Interactor', 'gloo_for_elementor' ),
			)
		);

		$element->add_control(
			$this->prefix . 'enable_conditions',
			[
				'label'        => __( 'Enable Conditions', 'gloo_for_elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
			]
		);


		$condition_repeater = new Repeater();

		$condition_repeater->add_control(
			$this->prefix . 'condition_type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Firing condition type', 'gloo_for_elementor' ),
				'label_block' => true,
				'default'     => 'show',
				'options'     => array(
					'show' => __( 'Use trigger only if condition met', 'gloo_for_elementor' ),
					'hide' => __( 'Don\'t use trigger  if condition met', 'gloo_for_elementor' ),
				),
			)
		);

		$condition_repeater->add_control(
			$this->prefix . 'condition',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Condition', 'gloo_for_elementor' ),
				'label_block' => true,
				'groups'      => Module::instance()->conditions->get_grouped_conditions_for_options(),
			)
		);
		global $wp_roles;
		$user_roles = array();

		foreach ( $wp_roles->roles as $role_id => $role ) {
			$user_roles[ $role_id ] = $role['name'];
		}

		$condition_repeater->add_control(
			$this->prefix . 'condition_user_role',
			array(
				'label'       => __( 'User role', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $user_roles,
				'label_block' => true,
				'condition'   => array(
					$this->prefix . 'condition' => array( 'user-role', 'user-role-not' ),
				),
			)
		);

		$condition_repeater->add_control(
			$this->prefix . 'condition_user_id',
			array(
				'label'       => __( 'User IDs', 'gloo_for_elementor' ),
				'description' => __( 'Set comma separated IDs list (10, 22, 19 etc.). Note: ID Guest user is 0', 'gloo' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'condition'   => array(
					$this->prefix . 'condition' => array( 'user-id', 'user-id-not' ),
				),
			)
		);

		$condition_repeater->add_control(
			$this->prefix . 'condition_field',
			array(
				'label'       => __( 'Field', 'gloo_for_elementor' ),
				'description' => __( 'Enter meta field name or select dynamic tag to compare value against. <br><b>Note!</b> If your meta field contains array you need to set meta field name manually (not with dynamic capability)', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						DynamicTags::BASE_GROUP,
						DynamicTags::TEXT_CATEGORY,
						DynamicTags::URL_CATEGORY,
						DynamicTags::GALLERY_CATEGORY,
						DynamicTags::IMAGE_CATEGORY,
						DynamicTags::MEDIA_CATEGORY,
						DynamicTags::POST_META_CATEGORY,
						DynamicTags::NUMBER_CATEGORY,
						DynamicTags::COLOR_CATEGORY,
					),
				),
				'condition'   => array(
					$this->prefix . 'condition' => Module::instance()->conditions->get_conditions_for_fields(),
				),
			)
		);


		$condition_repeater->add_control(
			$this->prefix . 'condition_value',
			array(
				'label'       => __( 'Value', 'gloo_for_elementor' ),
				'description' => __( 'Set value to compare. Separate values with commas to set values list.', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'condition'   => array(
					$this->prefix . 'condition' => Module::instance()->conditions->get_conditions_with_value_detect(),
				),
			)
		);

		$data_types = apply_filters( 'gloo/modules/interactor/data-types', array(
			'chars'   => __( 'Chars (alphabetical comparison)', 'gloo_for_elementor' ),
			'numeric' => __( 'Numeric', 'gloo_for_elementor' ),
			'date'    => __( 'Datetime', 'gloo_for_elementor' )
		) );


		$condition_repeater->add_control(
			$this->prefix . 'condition_data_type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Data type', 'gloo_for_elementor' ),
				'label_block' => true,
				'default'     => 'chars',
				'options'     => $data_types,
				'condition'   => array(
					$this->prefix . 'condition' => Module::instance()->conditions->get_conditions_with_type_detect(),
				),
			)
		);


		$condition_repeater->add_control(
			$this->prefix . 'condition_triggers',
			array(
				'type'        => Controls_Manager::SELECT2,
				'label'       => __( 'Triggers', 'gloo_for_elementor' ),
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_triggers(),
			)
		);

		$element->add_control(
			$this->prefix . 'conditions',
			[
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $condition_repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'condition}}}',
//				'title_field'   => '<# var gloo_interactor_conditions=' . $this->get_php_condition_label( '.{{{gloo_interactor_php_condition}}}.' ) . '; console.log(gloo_interactor_conditions);#> {{{ gloo_interactor_conditions }}}',
				'label_block'   => false,
				'condition'     => [
					$this->prefix . 'enable_conditions' => 'yes'
				],
			]
		);


		$trigger_repeater = new Repeater();

		$trigger_repeater->add_control(
			$this->prefix . 'trigger_title',
			[
				'label'       => __( 'Title', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Trigger Title', 'gloo_for_elementor' ),
			]
		);

		$trigger_repeater->add_control(
			$this->prefix . 'trigger_event',
			array(
				'type'    => Controls_Manager::SELECT2,
				'label'   => __( 'Firing Event', 'gloo_for_elementor' ),
				'options' => [
					'click'           => __( 'Click', 'gloo_for_elementor' ),
					'dblclick'        => __( 'Double Click', 'gloo_for_elementor' ),
					'hover'           => __( 'Hover', 'gloo_for_elementor' ),
					'mouseenter'      => __( 'Mouse Enter', 'gloo_for_elementor' ),
					'mouseleave'      => __( 'Mouse Leave', 'gloo_for_elementor' ),
					'keydown'         => __( 'Key Down', 'gloo_for_elementor' ),
					'keypress'        => __( 'Key Press', 'gloo_for_elementor' ),
					'keyup'           => __( 'Key Up', 'gloo_for_elementor' ),
					'submit'          => __( 'Submit', 'gloo_for_elementor' ),
					'ready'           => __( 'Ready', 'gloo_for_elementor' ),
					'change'          => __( 'Input Change', 'gloo_for_elementor' ),
					'scroll'          => 'Scroll',
					'ajaxComplete'    => 'After Any Ajax Call',
					'DOMNodeInserted' => __( 'Element Inserted in the Dom', 'gloo_for_elementor' ),
				],
			)
		);


		$trigger_repeater->add_control(
			$this->prefix . 'scroll_once',
			[
				'label'        => __( 'Fire once only', 'gloo_for_elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => [
					$this->prefix . 'trigger_event' => [ 'scroll' ]
				],
			]
		);


		$trigger_repeater->add_control(
			$this->prefix . 'fire_once',
			[
				'label'        => __( 'Fire once only', 'gloo_for_elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					$this->prefix . 'trigger_event!' => [ 'scroll', 'ajaxComplete' ],
				],
			]
		);


		$trigger_repeater->add_control(
			$this->prefix . 'scroll_position_only',
			[
				'label'        => __( 'Fire on specific scroll point', 'gloo_for_elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Will fire on every scroll if disabled.', 'gloo_for_elementor' ),
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					$this->prefix . 'trigger_event' => [ 'scroll' ]
				],
			]
		);

		$trigger_repeater->add_control(
			$this->prefix . 'scroll_position_compare',
			[
				'label'     => __( 'Scroll Comparison', 'gloo_for_elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '=',
				'options'   => [
					'==' => 'Equal (=)',
					'<'  => 'Less Than (<)',
					'>'  => 'Greater Than (>)',
				],
				'condition' => [
					$this->prefix . 'trigger_event'        => [ 'scroll' ],
					$this->prefix . 'scroll_position_only' => [ 'yes' ]
				],
			]
		);


		$trigger_repeater->add_control(
			$this->prefix . 'scroll_position',
			[
				'label'       => __( 'Scroll Position', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::SLIDER,
//				'size_units' => [ 'px', 'vh' , '%'],
				'description' => 'position in <b>px</b>.',
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 5,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'     => [
					'unit' => 'px',
				],
				'condition'   => [
					$this->prefix . 'trigger_event'        => [ 'scroll' ],
					$this->prefix . 'scroll_position_only' => [ 'yes' ]
				],
			]
		);

		$trigger_repeater->add_control(
			$this->prefix . 'trigger_target',
			[
				'label'       => __( 'Target', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( '#id, .class or HTML element.<br>use commas to separate multiple selectors.', 'gloo_for_elementor' ),
				'placeholder' => __( '#id, .class, element', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					$this->prefix . 'trigger_event!' => [ 'ready', 'scroll', 'ajaxComplete' ]

				],
			]
		);


		$trigger_repeater->add_control(
			$this->prefix . 'trigger_connect',
			[
				'type'     => Controls_Manager::SELECT2,
				'label'    => __( 'Event to fire', 'gloo_for_elementor' ),
				'options'  => $this->get_events(),
				'multiple' => true,
			]
		);


		$element->add_control(
			$this->prefix . 'triggers',
			[
				'label'         => __( 'Triggers', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $trigger_repeater->get_controls(),
				'title_field'   => '{{{ ' . $this->prefix . 'trigger_title }}}',
			]
		);


		$events_repeater = new Repeater();

		$events_repeater->add_control(
			$this->prefix . 'event_title',
			[
				'label'       => __( 'Title', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Event Title', 'gloo_for_elementor' ),
			]
		);

		$events_repeater->add_control(
			$this->prefix . 'event_type',
			array(
				'type'    => Controls_Manager::SELECT2,
				'label'   => __( 'Event', 'gloo_for_elementor' ),
				'options' => [
					$this->prefix . 'animations'      => __( 'Animations', 'gloo_for_elementor' ),
					$this->prefix . 'interactions'    => __( 'Interactions', 'gloo_for_elementor' ),
					$this->prefix . 'emulations'      => __( 'Emulations', 'gloo_for_elementor' ),
					$this->prefix . 'custom_function' => __( 'Custom Function (Advanced)', 'gloo_for_elementor' ),
				],
			)
		);


		$events_repeater->add_control(
			$this->prefix . 'animations',
			array(
				'type'      => Controls_Manager::SELECT2,
				'label'     => __( 'Animation', 'gloo_for_elementor' ),
				'options'   => [
					'hide'        => __( 'Hide', 'gloo_for_elementor' ),
					'show'        => __( 'Show', 'gloo_for_elementor' ),
					'toggle'      => __( 'Toggle Show/Hide', 'gloo_for_elementor' ),
					'fadeIn'      => __( 'Fade In', 'gloo_for_elementor' ),
					'fadeOut'     => __( 'Fade Out', 'gloo_for_elementor' ),
					'fadeToggle'  => __( 'Fade Toggle', 'gloo_for_elementor' ),
					'slideUp'     => __( 'Slide Up', 'gloo_for_elementor' ),
					'slideDown'   => __( 'Slide Down', 'gloo_for_elementor' ),
					'slideToggle' => __( 'Slide Toggle', 'gloo_for_elementor' ),
				],
				'condition' => [
					$this->prefix . 'event_type' => [
						$this->prefix . 'animations',
					]
				],
			)
		);


		$events_repeater->add_control(
			$this->prefix . 'interactions',
			array(
				'type'      => Controls_Manager::SELECT2,
				'label'     => __( 'Interaction', 'gloo_for_elementor' ),
				'options'   => [
					'val'         => __( 'Set Value', 'gloo_for_elementor' ),
					'alert'       => __( 'Alert', 'gloo_for_elementor' ),
					'addClass'    => __( 'Add Class', 'gloo_for_elementor' ),
					'removeClass' => __( 'Remove Class', 'gloo_for_elementor' ),
					'toggleClass' => __( 'Toggle Class', 'gloo_for_elementor' ),
					'attr'        => __( 'Set Attribute', 'gloo_for_elementor' ),
					'removeAttr'  => __( 'Remove Attribute', 'gloo_for_elementor' ),
					'prop'        => __( 'Set Prop', 'gloo_for_elementor' ),
					'removeProp'  => __( 'Remove Prop', 'gloo_for_elementor' ),
					'css'         => __( 'Set CSS', 'gloo_for_elementor' ),
					'text'        => __( 'Set Text', 'gloo_for_elementor' ),
					'append'      => __( 'Append', 'gloo_for_elementor' ),
					'appendTo'    => __( 'Append To', 'gloo_for_elementor' ),
					'prepend'     => __( 'Prepend', 'gloo_for_elementor' ),
					'prependTo'   => __( 'Prepend To', 'gloo_for_elementor' ),
					'before'      => __( 'Before', 'gloo_for_elementor' ),
					'after'       => __( 'After', 'gloo_for_elementor' ),
					'load'        => __( 'Load', 'gloo_for_elementor' ),
				],
				'condition' => [
					$this->prefix . 'event_type' => [
						$this->prefix . 'interactions',
					]
				],
			)
		);

		$events_repeater->add_control(
			$this->prefix . 'emulations',
			array(
				'type'      => Controls_Manager::SELECT2,
				'label'     => __( 'Emulation', 'gloo_for_elementor' ),
				'options'   => [
					'click'          => __( 'Click', 'gloo_for_elementor' ),
					'dblclick'       => __( 'Double Click', 'gloo_for_elementor' ),
					'hover'          => __( 'Hover', 'gloo_for_elementor' ),
					'preventDefault' => __( 'Prevent Default', 'gloo_for_elementor' ),

				],
				'condition' => [
					$this->prefix . 'event_type' => [
						$this->prefix . 'emulations',
					]
				],
			)
		);


		$events_repeater->add_control(
			$this->prefix . 'custom_function',
			[
				'label'       => __( 'Custom Function', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::CODE,
				'language'    => 'javascript',
				'description' => __( 'Only use custom functions if you know what you are doing, this can break your page.<br>You have access to jQuery with <b>$</b>', 'gloo_for_elementor' ),
				'placeholder' => __( '#id, .class', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					$this->prefix . 'event_type' => [
						$this->prefix . 'custom_function',
					]
				],
			]
		);


		$events_repeater->add_control(
			$this->prefix . 'event_target',
			[
				'label'       => __( 'Target', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( '#id, .class or HTML element.<br>use commas to separate multiple selectors.', 'gloo_for_elementor' ),
				'placeholder' => __( '#id, .class', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					$this->prefix . 'interactions!' => 'alert',
					$this->prefix . 'event_type!'   => $this->prefix . 'custom_function',
				],
			]
		);


		$events_repeater->add_control(
			$this->prefix . 'event_target_relation',
			[
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Target Relation', 'gloo' ),
				'default'     => '',
				'description' => 'Advanced: Used to target the child or parent of the Trigger element specifically, in case of no unique selector.',
				'options'     => array(
					''        => __( 'Default (None)', 'gloo' ),
					'find'    => __( 'Target is a child of the trigger element.', 'gloo' ),
					'closest' => __( 'Target is a parent of the trigger element.', 'gloo' ),
				),
				'condition'   => [
					$this->prefix . 'interactions!' => 'alert',
					$this->prefix . 'event_type!'   => $this->prefix . 'custom_function',
				],
			]
		);


		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter_dynamic',
			[
				'label'       => __( 'Fetch Form Field', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'description' => "First Parameter",
				'default'     => '',
				'condition'   => [
					$this->prefix . 'event_type'   => [
						$this->prefix . 'interactions'
					],
					$this->prefix . 'interactions' => [
						'addClass',
						'alert',
						'removeClass',
						'toggleClass',
						'attr',
						'prop',
						'removeProp',
						'removeAttr',
						'css',
						'append',
						'appendTo',
						'prepend',
						'prependTo',
						'before',
						'after',
						'val',
						'text',
						'load',
					],
				],
			]
		);

		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter_form',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( "Fetch Parameter", 'gloo_for_elementor' ),
				'placeholder' => '#id, .classs',
				'description' => "We recommend using an ID, if not, make sure the selector is unique.",
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => [
					$this->prefix . 'interactions'                        => [
						'addClass',
						'alert',
						'removeClass',
						'toggleClass',
						'attr',
						'prop',
						'removeProp',
						'removeAttr',
						'css',
						'append',
						'appendTo',
						'prepend',
						'prependTo',
						'before',
						'after',
						'val',
						'text',
						'load',
					],
					$this->prefix . 'event_interaction_parameter_dynamic' => 'yes'

				],
			)
		);

		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter_suffix',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( "Fetch Parameter Suffix", 'gloo_for_elementor' ),
				'placeholder' => 'Example: px',
				'description' => "(Optional) Append something to the dynamic fetched value.",
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => [
					$this->prefix . 'interactions'                        => [
						'addClass',
						'alert',
						'removeClass',
						'toggleClass',
						'attr',
						'prop',
						'removeProp',
						'removeAttr',
						'css',
						'append',
						'appendTo',
						'prepend',
						'prependTo',
						'before',
						'after',
						'val',
						'text',
						'load',
					],
					$this->prefix . 'event_interaction_parameter_dynamic' => 'yes'

				],
			)
		);

		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Parameter', 'gloo_for_elementor' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => [
					$this->prefix . 'event_type'                           => [
						$this->prefix . 'interactions'
					],
					$this->prefix . 'interactions'                         => [
						'addClass',
						'alert',
						'removeClass',
						'toggleClass',
						'attr',
						'prop',
						'removeProp',
						'removeAttr',
						'css',
						'append',
						'appendTo',
						'prepend',
						'prependTo',
						'before',
						'after',
						'val',
						'text',
						'load',
					],
					$this->prefix . 'event_interaction_parameter_dynamic!' => 'yes'
				],
			)
		);

		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter2_dynamic',
			[
				'label'        => __( 'Fetch Form Field', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => "Second Parameter",
				'condition'    => [
					$this->prefix . 'interactions' => [
						'attr',
						'css',
						'prop',
					],
				],
			]
		);

		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter2_file',
			[
				'label'       => __( 'Fetch Image From Input', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'description' => "Fetches the image from the file input field.",
				'condition'   => [
					$this->prefix . 'interactions'                         => [
						'attr',
						'css',
						'prop',
					],
					$this->prefix . 'event_interaction_parameter2_dynamic' => 'yes'
				],
			]
		);

		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter2_file_output',
			[
				'label'     => __( 'Image Output', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'raw',
				'options'   => [
					'raw'                  => 'Raw image value',
					'url'                  => 'Image wrapped in url()',
					'remove_double_quotes' => 'Raw and remove double quotes',
				],
				'condition' => [
					$this->prefix . 'interactions' => [
						'attr',
						'css',
						'prop',
					],
					//$this->prefix . 'event_interaction_parameter2_file' => 'yes'

				],
			]
		);


		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter2_form',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( "Fetch Parameter 2", 'gloo_for_elementor' ),
				'placeholder' => '#id, .classs',
				'description' => "We recommend using an ID, if not, make sure the selector is unique.",
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => [
					$this->prefix . 'interactions'                         => [
						'attr',
						'css',
						'prop',
					],
					$this->prefix . 'event_interaction_parameter2_dynamic' => 'yes'

				],
			)
		);


		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter2_form_attribute',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( "Fetch Parameter attribute", 'gloo_for_elementor' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter2_suffix',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( "Fetch Parameter Suffix", 'gloo_for_elementor' ),
				'placeholder' => 'Example: px',
				'description' => "(Optional) Append something to the dynamic fetched value.",
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => [
					$this->prefix . 'interactions'                         => [
						'attr',
						'css',
						'prop',
					],
					$this->prefix . 'event_interaction_parameter2_dynamic' => 'yes'

				],
			)
		);


		$events_repeater->add_control(
			$this->prefix . 'event_interaction_parameter2',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( "Second Parameter", 'gloo_for_elementor' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => [
					$this->prefix . 'interactions' => [
						'attr',
						'css',
						'prop',
					],

					$this->prefix . 'event_interaction_parameter2_dynamic!' => 'yes'
				],
			)
		);

		$events_repeater->add_control(
			$this->prefix . 'event_delay',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Delay (ms)', 'gloo_for_elementor' ),
				'description' => __( 'Delay before firing the current function', 'gloo_for_elementor' ),
				'placeholder' => 'None',
				'multiple'    => true,
			]
		);

		$events_repeater->add_control(
			$this->prefix . 'event_responsive',
			[
				'type'     => Controls_Manager::SELECT2,
				'label'    => __( 'Responsive', 'gloo_for_elementor' ),
				'options'  => [
					'mobile'  => 'Mobile',
					'tablet'  => 'Tablet',
					'desktop' => 'Desktop',
				],
				'default'  => [ 'mobile', 'tablet', 'desktop' ],
				'multiple' => true,
			]
		);

		$events_repeater->add_control(
			$this->prefix . 'event_next_status',
			[
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Enable Fire Next', 'gloo_for_elementor' ),
			]
		);


		$events_repeater->add_control(
			$this->prefix . 'event_next',
			[
				'type'        => Controls_Manager::SELECT2,
				'label'       => __( 'Fire Next', 'gloo_for_elementor' ),
				'description' => __( 'Choose one or more events to fire next', 'gloo_for_elementor' ),
				'options'     => $this->get_events(),
				'multiple'    => true,
				'condition'   => [
					$this->prefix . 'event_next_status' => 'yes',
				],
			]
		);

		$element->add_control(
			$this->prefix . 'events',
			[
				'label'         => __( 'Events', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $events_repeater->get_controls(),

				'title_field' => '{{{ ' . $this->prefix . 'event_title }}}',
			]
		);


		if ( apply_filters( 'gloo/modules/interactor/enable_variables', false ) ) {


			$variables_repeater = new Repeater();

			$variables_repeater->add_control(
				$this->prefix . 'variable_name',
				[
					'label'       => __( 'Name', 'gloo_for_elementor' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( 'Variable', 'gloo_for_elementor' ),
				]
			);
			$variables_repeater->add_control(
				$this->prefix . 'variable_value',
				[
					'label'       => __( 'Value', 'gloo_for_elementor' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( 'Value', 'gloo_for_elementor' ),
//					'dynamic'     => array(
//						'active'     => true,
//						'categories' => array(
//							DynamicTags::BASE_GROUP,
//							DynamicTags::TEXT_CATEGORY,
//							DynamicTags::URL_CATEGORY,
//							DynamicTags::GALLERY_CATEGORY,
//							DynamicTags::IMAGE_CATEGORY,
//							DynamicTags::MEDIA_CATEGORY,
//							DynamicTags::POST_META_CATEGORY,
//							DynamicTags::NUMBER_CATEGORY,
//							DynamicTags::COLOR_CATEGORY,
//						),
//					),
				]
			);

			$element->add_control(
				$this->prefix . 'variables',
				[
					'label'         => __( 'Variables', 'gloo_for_elementor' ),
					'type'          => Controls_Manager::REPEATER,
					'prevent_empty' => false,
					'fields'        => $variables_repeater->get_controls(),
					'title_field'   => '{{{ ' . $this->prefix . 'variable_name }}}',
//					'dynamic'       => array(
//						'active'     => true,
//						'categories' => array(
//							DynamicTags::BASE_GROUP,
//							DynamicTags::TEXT_CATEGORY,
//							DynamicTags::URL_CATEGORY,
//							DynamicTags::GALLERY_CATEGORY,
//							DynamicTags::IMAGE_CATEGORY,
//							DynamicTags::MEDIA_CATEGORY,
//							DynamicTags::POST_META_CATEGORY,
//							DynamicTags::NUMBER_CATEGORY,
//							DynamicTags::COLOR_CATEGORY,
//						),
//					),
				]
			);
		}

		$element->end_controls_section();
//		$element->end_injection();

	}

	public function check_loaded_documents() {

		$loaded_post_ids = $this->loaded_post_ids;

		if ( ! $loaded_post_ids || ! is_array( $loaded_post_ids ) ) {
			return;
		}

		$js_for_all_documents = '';

		if ( $this->jet_listing_ids ) {
			global $post;

			foreach ( $this->jet_listing_ids as $jet_main_listing_id => $jet_listing_item_ids ) {

				if ( ! $jet_listing_item_ids || ! is_array( $jet_listing_item_ids ) || count( $jet_listing_item_ids ) < 2 ) {
					continue;
				}

				// avoid duplicate JS print in the first jet listing item
				$first_key = key( $jet_listing_item_ids );
				unset( $jet_listing_item_ids[ $first_key ] );

				foreach ( $jet_listing_item_ids as $jet_listing_item_id ) {

					$main_listing_settings = ( \Elementor\Plugin::instance()->documents->get( $jet_main_listing_id ) )->get_settings();
					if ( is_object( \Elementor\Plugin::instance()->documents->get( $jet_listing_item_id ) ) ) {
						$sub_listing_og_settings = ( \Elementor\Plugin::instance()->documents->get( $jet_listing_item_id ) )->get_settings();
					} else {
						continue;
					}

					// set settings to main listing item
					$post = get_post( $jet_listing_item_id, OBJECT );
					setup_postdata( $post );


					$sub_listing = \Elementor\Plugin::instance()->documents->get( $jet_listing_item_id );

					$sub_listing->set_settings( $main_listing_settings );

					if ( $sub_listing->get_settings( $this->prefix . 'triggers' ) ) {
						$js_for_all_documents .= $this->get_js_for_document( $sub_listing );
					}

					// reset the settings
					$sub_listing->set_settings( $sub_listing_og_settings );
				}
			}
			wp_reset_query();
		}

		foreach ( $loaded_post_ids as $loaded_post_id ) {
			$document = \Elementor\Plugin::instance()->documents->get( $loaded_post_id );
			if ( ! empty( $document ) ) {
				if ( $document->get_settings( $this->prefix . 'triggers' ) ) {
					$js_for_all_documents .= $this->get_js_for_document( $document );
				}
			}
		}

		if ( $js_for_all_documents ) {
			$this->output_js_code( $js_for_all_documents );
		}


	}

	public function output_js_code( $js_functions ) {
		echo "<script type='text/javascript' id='gloo-interactor-output'>(function($) {
    		$(window).on( 'elementor/frontend/init' ,function(){
				$js_functions
			});
			})(jQuery);
			</script>";
	}

	public function generate_js_code( $args ) {

		$event            = $args['event'];
		$devices          = $args['devices'];
		$target           = $args['target'];
		$parameters       = $args['parameters'];
		$delay            = intval( $args['delay'] );
		$fire_next        = $args['next'];
		$fire_next_status = $args['event_next_status'];
		$event_id         = $args['event_id'];
		$relation         = $args['relation'];


		$target = $this->clean_special_chars( $target );

		if ( ! $devices ) { // not set to run on any devices
			return;
		}

		// set parameter if empty and using load event
		if ( $event === 'load' && empty( $parameters ) ) {
			$parameters = "' {$target}'";
		}

		$action = "{$event}({$parameters});";

		if ( $event !== 'alert' ) {
			$parent_or_child = '';
			if ( $relation === 'closest' || $relation === 'find' ) {
				$parent_or_child = "(target).$relation";
			}
			$action = "\${$parent_or_child}('{$target}')." . $action;
		}

		if ( $args['custom_function'] ) {
			$action = $args['custom_function'];
		}

		$responsive_conditions = $this->get_responsive_conditions( $devices );
		if ( count( $devices ) < 3 ) { // avoid if statement when all 3 devices are enabled
			$action = "if($responsive_conditions){
				$action
			}";
		}

		if ( $fire_next && $fire_next_status === 'yes' ) {

			foreach ( $fire_next as $fire_next_id ) {
				$current_post_id = get_the_ID();
				if ( ! isset( $this->active_functions[ $fire_next_id . $current_post_id ] ) ) {
					$this->undefined_functions[ $fire_next_id ] = $fire_next_id;
				}
				$action .= "interactor_{$fire_next_id}{$current_post_id}();";
			}

		}

		// add delay if it is set
		$action = $delay ? "setTimeout(function () { $action }, {$delay});" : "$action";

		// wrap in function
		$function = "var interactor_{$event_id} = (function interactor_{$event_id}(target) {
			$action
			return interactor_{$event_id};
		});";

		return $function;
	}

	public function get_responsive_targets( $devices, $target ) {
		$responsive_target = '';
		foreach ( $devices as $device ) {
			$responsive_target .= $responsive_target ? ', ' : '';
			$responsive_target .= "body[data-elementor-device-mode=\"$device\"] $target";
		}

		return $responsive_target;
	}

	public function get_responsive_conditions( $devices ) {
		$responsive_condition = '';
		foreach ( $devices as $device ) {
			$responsive_condition .= $responsive_condition ? ' || ' : '';
			$responsive_condition .= "elementorFrontend.getCurrentDeviceMode() == '$device'";
		}

		return $responsive_condition;
	}

	public function get_php_conditions() {
		$result = array(
			'general' => array(
				'label'   => __( 'Advanced', 'gloo_for_elementor' ),
				'options' => array(
					'get_parameter'  => '$_GET Parameter',
					'post_parameter' => '$_POST Parameter',
				),
			),
			'user'    => array(
				'label'   => __( 'User', 'gloo_for_elementor' ),
				'options' => array(
					'is_logged_in' => 'Is logged in',
					'is_verified'  => 'Is Verified',
				),
			),

		);

		return $result;
	}

	public function get_js_code_from_triggers( $triggers, $triggers_to_ignore ) {

		$js_functions    = '';
		$current_post_id = get_the_ID();
		foreach ( $triggers as $key => $trigger ) {
			// check if its set to be ignored
			if ( $triggers_to_ignore && in_array( $trigger['_id'], $triggers_to_ignore, true ) ) {
				continue;
			}

			do_action( 'gloo/modules/interactor/trigger_loop_item', $trigger );

			$event       = $trigger[ $this->prefix . 'trigger_event' ];
			$target      = $this->clean_special_chars( $trigger[ $this->prefix . 'trigger_target' ] );
			$connections = $trigger[ $this->prefix . 'trigger_connect' ];

			$trigger_functions = apply_filters( "gloo/modules/interactor/trigger_loop_item/trigger_functions/{$trigger['_id']}", '' );

			// skip if one is missing
			if ( ! $target && ( $event != 'ready' && $event != 'scroll' && $event != 'ajaxComplete' ) ) {
				continue;
			}


			// check for trigger to event connections and generate js for the events
			if ( $connections && $event ) {
				foreach ( $connections as $event_id ) {
//					$event_id .=  $current_post_id;
					if ( $this->generate_js_for_event( $event_id ) ) {
						$trigger_functions .= "interactor_{$event_id}{$current_post_id}();";
					}
				}
			}


			if ( $trigger_functions ) {
				$js_functions .= $this->prepare_the_function( $trigger, $trigger_functions );
			}
		}

		return $js_functions;
	}

	public function generate_js_for_event( $event_id ) {


		if ( ! $this->events ) {
			return false;
		}

		$current_event = '';
		foreach ( $this->events as $event ) {
			if ( ! isset( $event['_id'] ) || $event['_id'] !== $event_id ) {
				continue;
			}
			$current_event = $event;
			break;
		}


		if ( ! $current_event ) {
			return false;
		}

		$type = $current_event[ $this->prefix . 'event_type' ];


		if ( ! isset( $current_event[ $type ] ) || ! $current_event[ $type ] ) {
			return false;
		}


		$parameters = '';
		$parameter1 = isset( $current_event[ $this->prefix . 'event_interaction_parameter' ] ) ? $current_event[ $this->prefix . 'event_interaction_parameter' ] : '';
		$parameter2 = isset( $current_event[ $this->prefix . 'event_interaction_parameter2' ] ) ? $current_event[ $this->prefix . 'event_interaction_parameter2' ] : '';
		if ( $parameter1 || $parameter2 || $current_event[ $this->prefix . 'event_interaction_parameter_dynamic' ] || $current_event[ $this->prefix . 'event_interaction_parameter2_dynamic' ] ) {
			$parameter1 = "'" . $this->clean_special_chars( $parameter1 ) . "'";
			$parameter2 = "'" . $this->clean_special_chars( $parameter2 ) . "'";

			if ( $current_event[ $this->prefix . 'event_interaction_parameter_dynamic' ] === 'yes' ) {
				$parameter1 = "$('{$current_event[ $this->prefix . 'event_interaction_parameter_form' ]}').val() + '{$current_event[ $this->prefix . 'event_interaction_parameter_suffix' ]}'";


				if ( ! empty( $current_event[ $this->prefix . 'event_interaction_parameter2_form_attribute' ] ) ) {
					$parameter1 = "$('{$current_event[ $this->prefix . 'event_interaction_parameter_form' ]}').attr('" . $current_event[ $this->prefix . 'event_interaction_parameter2_form_attribute' ] . "') + '{$current_event[ $this->prefix . 'event_interaction_parameter_suffix' ]}'";
					// $parameter2 = "$('{$current_event[ $this->prefix . 'event_interaction_parameter2_form' ]}'). + '{$current_event[ $this->prefix . 'event_interaction_parameter2_suffix' ]}'";
				}
			}


			if ( $current_event[ $this->prefix . 'event_interaction_parameter2_dynamic' ] === 'yes' ) {

				$parameter2 = "$('{$current_event[ $this->prefix . 'event_interaction_parameter2_form' ]}').val() + '{$current_event[ $this->prefix . 'event_interaction_parameter2_suffix' ]}'";

				if ( $current_event[ $this->prefix . 'event_interaction_parameter2_file' ] ) {
					$parameter2 = "($('{$current_event[ $this->prefix . 'event_interaction_parameter2_form' ]}').prop('files') && $('{$current_event[ $this->prefix . 'event_interaction_parameter2_form' ]}').prop('files')[0] ? URL.createObjectURL($('{$current_event[ $this->prefix . 'event_interaction_parameter2_form' ]}').prop('files')[0]) : '')";
				}

				if ( $current_event[ $this->prefix . 'event_interaction_parameter2_file_output' ] === 'url' ) {
					$parameter2 = "'url('+{$parameter2}+')'";
				}

				if ( $current_event[ $this->prefix . 'event_interaction_parameter2_file_output' ] === 'remove_double_quotes' ) {
					$parameter2 = "$('{$current_event[ $this->prefix . 'event_interaction_parameter2_form' ]}').val().replace(/(^\"|\"$)/g, '') + '{$current_event[ $this->prefix . 'event_interaction_parameter2_suffix' ]}'";
				}


			}

			$parameters = $parameter1 && $parameter2 ? "$parameter1, $parameter2" : "$parameter1";
		}


		// append post ID to make the event ID unique
		$event_id .= get_the_ID();

		$args = [
			'event'             => $current_event[ $type ],
			'target'            => $current_event[ $this->prefix . 'event_target' ],
			'relation'          => $current_event[ $this->prefix . 'event_target_relation' ],
			'delay'             => $current_event[ $this->prefix . 'event_delay' ],
			'next'              => $current_event[ $this->prefix . 'event_next' ],
			'event_next_status' => $current_event[ $this->prefix . 'event_next_status' ],
			'devices'           => $current_event[ $this->prefix . 'event_responsive' ],
			'custom_function'   => $current_event[ $this->prefix . 'custom_function' ],
			'parameters'        => $parameters,
			'event_id'          => $event_id,
		];

		$args = apply_filters( 'gloo/modules/interactor/generate_js_for_event/args', $args, $current_event );

		$this->active_functions[ $event_id ] = $this->generate_js_code( $args );

		if ( $this->get_undefined_functions( $event_id ) ) {
			unset( $this->undefined_functions[ $event_id ] );
		}

		return $this->active_functions[ $event_id ];

	}

	public function set_active_functions( $id, $code ) {
		$this->active_functions[ $id ] = $code;
	}

	public function get_active_functions( $id ) {
		if ( ! isset( $this->active_functions[ $id ] ) ) {
			return false;
		}

		return $this->active_functions[ $id ];
	}

	public function set_undefined_functions( $id, $code ) {
		$this->undefined_functions[ $id ] = $code;
	}

	public function get_undefined_functions( $id ) {
		if ( ! isset( $this->undefined_functions[ $id ] ) ) {
			return false;
		}

		return $this->undefined_functions[ $id ];
	}

	public function get_current_document() {
		return $this->current_document;
	}

	public function set_current_document( $element ) {
		$this->current_document = $element;
	}

	public function get_js_for_document( $element ) {

		$this->set_current_document( $element );
		
		do_action('gloo/modules/interactor/before_js_code', $element);

		// check if trigger is disabled by a condition
		$triggers_to_ignore = Module::instance()->triggers_to_ignore( $element );

		// return if triggers are empty
		if ( ! $element->get_settings_for_display( $this->prefix . 'triggers' ) ) {
			return;
		}

		$triggers     = $element->get_settings_for_display( $this->prefix . 'triggers' );
		$this->events = $element->get_settings_for_display( $this->prefix . 'events' );

		$js_code = $this->get_js_code_from_triggers( $triggers, $triggers_to_ignore );


		if ( $this->undefined_functions ) {
			foreach ( $this->undefined_functions as $undefined_event ) {
				$this->generate_js_for_event( $undefined_event );
			}
			$this->undefined_functions = [];
		}


		$js_code = apply_filters( 'gloo/modules/interactor/js_code', $js_code );

		if ( $js_code ) {
			if ( $this->is_debug_mode() ) {

				if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '<' ) ) {
					$edit_url = \Elementor\Utils::get_edit_link( $element->get_id() );
				} else {
					$edit_url = \Elementor\Plugin::$instance->documents->get( $element->get_id() )->get_edit_url();
				}

				$js_code .= 'console.log( { 
				templateName: "' . $element->get_post()->post_title . '",
				editPostURL: "' . $edit_url . '",
				templateID: "' . $element->get_id() . '",
				code: ' . json_encode( $js_code, JSON_HEX_TAG ) . '
				});';
			}
		}

		$js_functions = apply_filters( 'gloo/modules/interactor/before_js_functions', '' );

		foreach ( $this->active_functions as $event_functions ) {
			$js_functions .= $event_functions;
		}

		$js_functions = apply_filters( 'gloo/modules/interactor/after_js_functions', $js_functions );

		return apply_filters( 'gloo/modules/interactor/document_js_code', $js_functions . $js_code );
	}

	public function prepare_the_function( $trigger, $js_code ) {

		$event  = $trigger[ $this->prefix . 'trigger_event' ];
		$target = $this->clean_special_chars( $trigger[ $this->prefix . 'trigger_target' ] );


		if ( $event == 'scroll' ) {


			$scroll_position         = $trigger[ $this->prefix . 'scroll_position' ];
			$scroll_position_value   = intval( $scroll_position['size'] );
			$scroll_position_compare = $trigger[ $this->prefix . 'scroll_position_compare' ];
			$scroll_position_only    = $trigger[ $this->prefix . 'scroll_position_only' ];
			$scroll_once             = $trigger[ $this->prefix . 'scroll_once' ] === 'yes' ? "jQuery(window).off('scroll');" : '';


			if ( $scroll_once ) {
				$js_code .= $scroll_once;
			}

			if ( $scroll_position_only === 'yes' ) {
				if ( ! in_array( $scroll_position_compare, [ '==', '<', '>' ] ) ) {
					$scroll_position_compare = '==';
				}

				$js_code = "if((window.pageYOffset || document.documentElement.scrollTop) {$scroll_position_compare} {$scroll_position_value} ){
					$js_code
				}";
			}

			return "$(window).on('$event', function (e){
			// fired by $event
			$js_code
			});";
		}


		if ( $this->is_debug_mode() ) {
			$js_code .= 'console.log( {
				target: "' . $target . '",
				event: "' . $event . '",
				code: ' . json_encode( $js_code, JSON_HEX_TAG ) . ',
				trace: "Fired by ' . $event . ' on ' . $target . '",
				});';
		}

		if ( $event === 'ready' ) {
			return $js_code;
		}

		if ( $event === 'ajaxComplete' ) {
			return "$(document).ajaxComplete(function(e){
			// fired by $event
			$js_code
			});";
		}

		$on_or_one = $trigger[ $this->prefix . 'fire_once' ] === 'yes' ? 'one' : 'on';

		return "$(document).{$on_or_one}('$event', '$target', function (e){
		// fired by $event on $target
		$js_code
		});";
	}

	public function get_loaded_ids( $content, $post_id ) {

		if ( ! \Elementor\Plugin::instance()->db->is_built_with_elementor( $post_id ) ) {
			return $content;
		}

		$this->loaded_post_ids[ $post_id ] = $post_id;

		return $content;

	}

	public function is_debug_mode() {
		return boolval( get_option( 'gloo_interactor_settings_debug' ) );
	}

	public function clean_special_chars( $string ) {
		return preg_replace( '/["\']/', '', $string );
	}

}