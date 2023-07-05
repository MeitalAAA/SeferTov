<?php

namespace Gloo\Modules\Interactor_Gsap;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use Gloo\Modules\Interactor\Module as Interactor;

class Settings {

	private $prefix = 'gloo_interactor_gsap_';

	public function __construct() {
		add_action( 'elementor/element/before_section_end', [ $this, 'add_settings' ], 10, 2 );
		add_action( 'gloo/modules/interactor/trigger_loop_item', [ $this, 'check_gsap_settings' ] );
		// print out js
		add_action('gloo/modules/interactor/before_js_code', [ $this, 'interactor_before_js_code' ]);

		if(!is_admin()){
			add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}else{
			// add javascript and css to wp-admin dashboard.
			// add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}
	}

	public function interactor_before_js_code( $element ) {
		
		$interactor_settings       = Interactor::instance()->settings;
		$current_document_settings = $interactor_settings->get_current_document()->get_settings_for_display();
		if ( ! isset( $current_document_settings[ $this->prefix ] ) ) {
			return;
		}

		$gsap_items = $current_document_settings[ $this->prefix ];
		
		if($current_document_settings[ $this->prefix .'enable_timeline' ] && $current_document_settings[ $this->prefix .'enable_timeline' ] == 'yes' && is_array($gsap_items) && count($gsap_items) >= 1){
			echo '<script type="text/javascript">jQuery(document).ready(function($){';
			$gsap_timeline_var_name = 'gsap_timeline_'.$this->generate_key(12);

			$gsap_settings = array();

			if($current_document_settings[$this->prefix . 'timeline_duration'])
				$gsap_settings['duration'] = $current_document_settings[$this->prefix . 'timeline_duration'];
			else
				$gsap_settings['duration'] = 2;
			
			if($current_document_settings[$this->prefix . 'timeline_translate_x'])
				$gsap_settings['x'] = $current_document_settings[$this->prefix . 'timeline_translate_x'];
			if($current_document_settings[$this->prefix . 'timeline_translate_y'])
				$gsap_settings['y'] = $current_document_settings[$this->prefix . 'timeline_translate_y'];
			if($current_document_settings[$this->prefix . 'timeline_easing'])
				$gsap_settings['ease'] = $current_document_settings[$this->prefix . 'timeline_easing'];
			if($current_document_settings[$this->prefix . 'timeline_opacity'] || $current_document_settings[$this->prefix . 'timeline_opacity'] == '0')
				$gsap_settings['opacity'] = $current_document_settings[$this->prefix . 'timeline_opacity'];
			if($current_document_settings[$this->prefix . 'timeline_rotate'])
				$gsap_settings['rotation'] = $current_document_settings[$this->prefix . 'timeline_rotate'];
			
			if($current_document_settings[$this->prefix . 'timeline_scale_x'])
				$gsap_settings['scaleX'] = $current_document_settings[$this->prefix . 'timeline_scale_x'];

			if($current_document_settings[$this->prefix . 'timeline_scale_y'])
				$gsap_settings['scaleY'] = $current_document_settings[$this->prefix . 'timeline_scale_y'];

			if($current_document_settings[$this->prefix . 'timeline_stagger'])
				$gsap_settings['stagger'] = $current_document_settings[$this->prefix . 'timeline_stagger'];
			if($current_document_settings[$this->prefix . 'timeline_delay'])
				$gsap_settings['delay'] = $current_document_settings[$this->prefix . 'timeline_delay'];
			
			if($current_document_settings[$this->prefix . 'timeline_custom_arguments']){
				$gsap_settings = $this->add_custom_args($gsap_settings, $current_document_settings[$this->prefix . 'timeline_custom_arguments']);
			}


			if($current_document_settings[$this->prefix . 'enable_timeline_scrolling'] && $current_document_settings[$this->prefix . 'enable_timeline_scrolling'] == 'yes'){

				if($current_document_settings[$this->prefix . 'tsp_trigger_element'])
					$gsap_settings['scrollTrigger']['trigger'] = $current_document_settings[$this->prefix . 'tsp_trigger_element'];
				if($current_document_settings[$this->prefix . 'tsp_trigger_start'])
					$gsap_settings['scrollTrigger']['start'] = $current_document_settings[$this->prefix . 'tsp_trigger_start'];
				if($current_document_settings[$this->prefix . 'tsp_trigger_end'])
					$gsap_settings['scrollTrigger']['end'] = $current_document_settings[$this->prefix . 'tsp_trigger_end'];
				if($current_document_settings[$this->prefix . 'tsp_show_markers'] && $current_document_settings[$this->prefix . 'tsp_show_markers'] == 'yes')
					$gsap_settings['scrollTrigger']['markers'] = true;
				if($current_document_settings[$this->prefix . 'tsp_trigger_scrub'])
					$gsap_settings['scrollTrigger']['scrub'] = $current_document_settings[$this->prefix . 'tsp_trigger_scrub'];
				if($current_document_settings[$this->prefix . 'tsp_pin_element'])
					$gsap_settings['scrollTrigger']['pin'] = $current_document_settings[$this->prefix . 'tsp_pin_element'];
				if($current_document_settings[$this->prefix . 'tsp_pin_spacing'] && $current_document_settings[$this->prefix . 'tsp_pin_spacing'] == 'yes')
					$gsap_settings['scrollTrigger']['pinSpacing'] = false;
				
				if($current_document_settings[$this->prefix . 'tsp_action_on_enter'] != 'none' || $current_document_settings[$this->prefix . 'tsp_action_on_leave'] != 'none' || $current_document_settings[$this->prefix . 'tsp_action_on_enter_back'] != 'none' || $current_document_settings[$this->prefix . 'tsp_action_on_leave_back'] != 'none'){
					$gsap_settings['scrollTrigger']['toggleActions'] = $current_document_settings[$this->prefix . 'tsp_action_on_enter'].' '.$current_document_settings[$this->prefix . 'tsp_action_on_leave'].' '.$current_document_settings[$this->prefix . 'tsp_action_on_enter_back'].' '.$current_document_settings[$this->prefix . 'tsp_action_on_leave_back'];
				}
			}
			// if($single_gsap_item[$this->prefix . 'event_scrolling'] && $single_gsap_item[$this->prefix . 'event_scrolling'] == 'yes'){

			// 	if($single_gsap_item[$this->prefix . 'trigger_element'])
			// 		$gsap_settings['scrollTrigger']['trigger'] = $single_gsap_item[$this->prefix . 'trigger_element'];
			// 	if($single_gsap_item[$this->prefix . 'trigger_start'])
			// 		$gsap_settings['scrollTrigger']['start'] = $single_gsap_item[$this->prefix . 'trigger_start'];
			// 	if($single_gsap_item[$this->prefix . 'trigger_end'])
			// 		$gsap_settings['scrollTrigger']['end'] = $single_gsap_item[$this->prefix . 'trigger_end'];
			// 	if($single_gsap_item[$this->prefix . 'show_markers'] && $single_gsap_item[$this->prefix . 'show_markers'] == 'yes')
			// 		$gsap_settings['scrollTrigger']['markers'] = true;
			// 	if($single_gsap_item[$this->prefix . 'trigger_scrub'])
			// 		$gsap_settings['scrollTrigger']['scrub'] = $single_gsap_item[$this->prefix . 'trigger_scrub'];
			// 	if($single_gsap_item[$this->prefix . 'pin_element'])
			// 		$gsap_settings['scrollTrigger']['pin'] = $single_gsap_item[$this->prefix . 'pin_element'];
			// 	if($single_gsap_item[$this->prefix . 'pin_spacing'] && $single_gsap_item[$this->prefix . 'pin_spacing'] == 'yes')
			// 		$gsap_settings['scrollTrigger']['pinSpacing'] = false;
				
			// 	if($single_gsap_item[$this->prefix . 'action_on_enter'] != 'none' || $single_gsap_item[$this->prefix . 'action_on_leave'] != 'none' || $single_gsap_item[$this->prefix . 'action_on_enter_back'] != 'none' || $single_gsap_item[$this->prefix . 'action_on_leave_back'] != 'none'){
			// 		$gsap_settings['scrollTrigger']['toggleActions'] = $single_gsap_item[$this->prefix . 'action_on_enter'].' '.$single_gsap_item[$this->prefix . 'action_on_leave'].' '.$single_gsap_item[$this->prefix . 'action_on_enter_back'].' '.$single_gsap_item[$this->prefix . 'action_on_leave_back'];
			// 	}
				
				
			// }

			$output_js = 'var '.$gsap_timeline_var_name.' =  gsap.timeline({defaults: '.json_encode($gsap_settings).'});';
			
			foreach ( $gsap_items as $single_gsap_item ) {
				$output_js .= $this->generate_js_code($single_gsap_item['_id'], $gsap_timeline_var_name);
			}
			echo $output_js;
			echo '});</script>';
		}
	}

	public function add_custom_args($gsap_settings, $custom_args){
		if($custom_args){
			$custom_args = preg_replace( '/\s*/m', '', $custom_args);
			$custom_args = explode(',', $custom_args);
			if(is_array($custom_args) && count($custom_args) >= 1){
				foreach($custom_args as $value){
					$value_key = explode(':', $value);
					if(is_array($value_key) && count($value_key) >= 1 && isset($value_key[1])){
						$gsap_settings[$value_key[0]] = $value_key[1];
					}
				}
			}
		}
		// db($gsap_settings);exit();
		return $gsap_settings;
	}
 
	public function check_gsap_settings( $trigger ) {

		if ( ! isset( $trigger[ $this->prefix . 'triggers' ] ) || ! $trigger[ $this->prefix . 'triggers' ] ) {
			return;
		}

		$interactor_settings       = Interactor::instance()->settings;
		$current_document_settings = $interactor_settings->get_current_document()->get_settings_for_display();
		if($current_document_settings[ $this->prefix .'enable_timeline' ] && $current_document_settings[ $this->prefix .'enable_timeline' ] == 'yes')
			return;

		// wp_enqueue_script( 'gsap' );

		$gsap_items = $trigger[ $this->prefix . 'triggers' ];
		$code = '';
		foreach ( $gsap_items as $gsap_item ) {
			// $code .= 'gsap.to(".wp-block-post-title", {duration:2, x:300});';
			$code .= $this->generate_js_code( $gsap_item );
		}
		add_filter( "gloo/modules/interactor/trigger_loop_item/trigger_functions/{$trigger['_id']}", function () use ( $code ) {
			return $code;
		} );
	}

	public function generate_js_code( $gsap_item, $timeline = 'gsap') {

		$interactor_settings       = Interactor::instance()->settings;
		$current_document_settings = $interactor_settings->get_current_document()->get_settings_for_display();
		if ( ! isset( $current_document_settings[ $this->prefix ] ) ) {
			return;
		}
		$gsap_items = $current_document_settings[ $this->prefix ];
		
		$output_js = '';
		foreach ( $gsap_items as $single_gsap_item ) {
			if($single_gsap_item['_id'] != $gsap_item)
				continue;
			// $output_js .= 'gsap.'.$single_gsap_item[$this->prefix . 'function'].'("'.$single_gsap_item[$this->prefix . 'target'].'", {duration:2, x:300});';
			$gsap_settings = array();

			if(!$single_gsap_item[$this->prefix . 'duration'])
				$single_gsap_item[$this->prefix . 'duration'] = 2;

			$gsap_settings['duration'] = $single_gsap_item[$this->prefix . 'duration'];

			if($single_gsap_item[$this->prefix . 'translate_x'])
				$gsap_settings['x'] = $single_gsap_item[$this->prefix . 'translate_x'];
			if($single_gsap_item[$this->prefix . 'translate_y'])
				$gsap_settings['y'] = $single_gsap_item[$this->prefix . 'translate_y'];
			if($single_gsap_item[$this->prefix . 'easing'])
				$gsap_settings['ease'] = $single_gsap_item[$this->prefix . 'easing'];
			if($single_gsap_item[$this->prefix . 'opacity'] || $single_gsap_item[$this->prefix . 'opacity'] === '0')
				$gsap_settings['opacity'] = $single_gsap_item[$this->prefix . 'opacity'];
			if($single_gsap_item[$this->prefix . 'rotate'])
				$gsap_settings['rotation'] = $single_gsap_item[$this->prefix . 'rotate'];
			
			if($single_gsap_item[$this->prefix . 'scale_x'])
				$gsap_settings['scaleX'] = $single_gsap_item[$this->prefix . 'scale_x'];

			if($single_gsap_item[$this->prefix . 'scale_y'])
				$gsap_settings['scaleY'] = $single_gsap_item[$this->prefix . 'scale_y'];

			if($single_gsap_item[$this->prefix . 'stagger'])
				$gsap_settings['stagger'] = $single_gsap_item[$this->prefix . 'stagger'];
			if($single_gsap_item[$this->prefix . 'delay'])
				$gsap_settings['delay'] = $single_gsap_item[$this->prefix . 'delay'];

			if($single_gsap_item[$this->prefix . 'custom_arguments']){
				$gsap_settings = $this->add_custom_args($gsap_settings, $single_gsap_item[$this->prefix . 'custom_arguments']);
			}
			
			if($single_gsap_item[$this->prefix . 'event_scrolling'] && $single_gsap_item[$this->prefix . 'event_scrolling'] == 'yes'){

					if($single_gsap_item[$this->prefix . 'trigger_element'])
						$gsap_settings['scrollTrigger']['trigger'] = $single_gsap_item[$this->prefix . 'trigger_element'];
					if($single_gsap_item[$this->prefix . 'trigger_start'])
						$gsap_settings['scrollTrigger']['start'] = $single_gsap_item[$this->prefix . 'trigger_start'];
					if($single_gsap_item[$this->prefix . 'trigger_end'])
						$gsap_settings['scrollTrigger']['end'] = $single_gsap_item[$this->prefix . 'trigger_end'];
					if($single_gsap_item[$this->prefix . 'show_markers'] && $single_gsap_item[$this->prefix . 'show_markers'] == 'yes' && $current_document_settings[$this->prefix . 'tsp_show_markers'] != 'yes')
						$gsap_settings['scrollTrigger']['markers'] = true;
					if($single_gsap_item[$this->prefix . 'trigger_scrub'])
						$gsap_settings['scrollTrigger']['scrub'] = $single_gsap_item[$this->prefix . 'trigger_scrub'];
					if($single_gsap_item[$this->prefix . 'pin_element'])
						$gsap_settings['scrollTrigger']['pin'] = $single_gsap_item[$this->prefix . 'pin_element'];
					if($single_gsap_item[$this->prefix . 'pin_spacing'] && $single_gsap_item[$this->prefix . 'pin_spacing'] == 'yes')
						$gsap_settings['scrollTrigger']['pinSpacing'] = false;
					
					if($single_gsap_item[$this->prefix . 'action_on_enter'] != 'none' || $single_gsap_item[$this->prefix . 'action_on_leave'] != 'none' || $single_gsap_item[$this->prefix . 'action_on_enter_back'] != 'none' || $single_gsap_item[$this->prefix . 'action_on_leave_back'] != 'none'){
						$gsap_settings['scrollTrigger']['toggleActions'] = $single_gsap_item[$this->prefix . 'action_on_enter'].' '.$single_gsap_item[$this->prefix . 'action_on_leave'].' '.$single_gsap_item[$this->prefix . 'action_on_enter_back'].' '.$single_gsap_item[$this->prefix . 'action_on_leave_back'];
					}
					
					
			}

			
				
				
			// }
			$target_element = 'body';
			if($single_gsap_item[$this->prefix . 'target'])
			$target_element = $single_gsap_item[$this->prefix . 'target'];
			$output_js .= $timeline.'.'.$single_gsap_item[$this->prefix . 'function'].'("'.$target_element.'", '.json_encode($gsap_settings).');';

			
		}

		return $output_js;
	}

	public function generate_key($length = 40)
  {
      $keyset = 'abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      $key    = '';

      for ($i = 0; $i < $length; $i++) {
          $key .= substr($keyset, wp_rand(0, strlen($keyset) - 1), 1);
      }

      return $key;
  }

	public function add_settings( $element, $section_id ) {

		if ( $section_id !== 'gloo_interactor_' ) {
			return;
		}

		$interactor_settings = Interactor::instance()->settings;

		// add gsap events to interactor triggers
		$interactor_triggers = $element->get_controls( 'gloo_interactor_triggers' );

		$interactor_triggers['fields'][ $this->prefix . 'triggers' ] = [
			'label'    => __( 'GSAP Events', 'gloo' ),
			'type'     => \Elementor\Controls_Manager::SELECT2,
			'options'  => $interactor_settings->get_settings_as_options( $this->prefix, $this->prefix . 'title' ),
			'name'     => $this->prefix . 'triggers',
			'multiple' => true,
		];
		$element->update_control( 'gloo_interactor_triggers', $interactor_triggers );

		$actions_array = array(
			'none' => 'None',
			'play' => 'Play',
			'pause' => 'Pause',
			'resume' => 'Resume',
			'reverse' => 'Reverse',
			'restart' => 'Restart',
			'complete' => 'Complete',
		);

		$ease_array = array(
			'Power0.easeNone' => 'Power0.easeNone',
			'Power0.easeIn' => 'Power0.easeIn',
			'Power0.easeInOut' => 'Power0.easeInOut',
			'Power0.easeOut' => 'Power0.easeOut',

			'Power1.easeNone' => 'Power1.easeNone',
			'Power1.easeIn' => 'Power1.easeIn',
			'Power1.easeInOut' => 'Power1.easeInOut',
			'Power1.easeOut' => 'Power1.easeOut',

			'Power2.easeNone' => 'Power2.easeNone',
			'Power2.easeIn' => 'Power2.easeIn',
			'Power2.easeInOut' => 'Power2.easeInOut',
			'Power2.easeOut' => 'Power2.easeOut',

			'Power3.easeNone' => 'Power3.easeNone',
			'Power3.easeIn' => 'Power3.easeIn',
			'Power3.easeInOut' => 'Power3.easeInOut',
			'Power3.easeOut' => 'Power3.easeOut',

			'Power4.easeNone' => 'Power4.easeNone',
			'Power4.easeIn' => 'Power4.easeIn',
			'Power4.easeInOut' => 'Power4.easeInOut',
			'Power4.easeOut' => 'Power4.easeOut',

			
			'Back.easeIn' => 'Back.easeIn',
			'Back.easeInOut' => 'Back.easeInOut',
			'Back.easeOut' => 'Back.easeOut',

			'Bounce.easeIn' => 'Bounce.easeIn',
			'Bounce.easeInOut' => 'Bounce.easeInOut',
			'Bounce.easeOut' => 'Bounce.easeOut',

			'Circ.easeIn' => 'Circ.easeIn',
			'Circ.easeInOut' => 'Circ.easeInOut',
			'Circ.easeOut' => 'Circ.easeOut',

			'Elastic.easeIn' => 'Elastic.easeIn',
			'Elastic.easeInOut' => 'Elastic.easeInOut',
			'Elastic.easeOut' => 'Elastic.easeOut',

			'Sine.easeIn' => 'Sine.easeIn',
			'Sine.easeInOut' => 'Sine.easeInOut',
			'Sine.easeOut' => 'Sine.easeOut',
		);



		$element->add_control(
			$this->prefix .'enable_gsap',
			[
			  'type' => \Elementor\Controls_Manager::SWITCHER,
			  'label' => __( 'Enable GSAP', 'gloo_for_elementor' ),
			//   'separator' => 'after',
			//   'label_block' => true,
				// 'conditions' => [
				// 	'relation' => 'and',
				// 	'terms' => [
				// 		[
				// 			'name' => $this->prefix .'enable_gsap',
				// 			'operator' => '==',
				// 			'value' => 'yes'
				// 		],
				// 	]
				// ]
			]
		  );








		  $element->add_control(
			$this->prefix .'enable_timeline',
			[
			  'type' => \Elementor\Controls_Manager::SWITCHER,
			  'label' => __( 'Enable Timeline', 'gloo_for_elementor' ),
			  'description' => __( 'Timeline enables assure that the events below run in a timeline.', 'gloo_for_elementor' ),
			  'separator' => 'before',
			  'conditions' => [
				'relation' => 'and',
				'terms' => [
					[
						'name' => $this->prefix .'enable_gsap',
						'operator' => '==',
						'value' => 'yes'
					],
				]
			]
			]
		  );

		  $element->add_control(
			$this->prefix .'timeline_parameters',
			[
				'label' => __( 'Timeline Parameters', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				// 'label_off' => __( 'Default', 'gloo_for_elementor' ),
				// 'label_on' => __( 'Custom', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'enable_gsap',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => $this->prefix .'enable_timeline',
							'operator' => '==',
							'value' => 'yes'
						],
					]
					],
				
				// 'label_block' => true,
			]
		);
		$element->start_popover();
		$element->add_control(
			$this->prefix . 'timeline_duration',
			[
				'label'       => __( 'Duration (sec)', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$element->add_control(
			$this->prefix . 'timeline_delay',
			[
				'label'       => __( 'Delay \ Offset (sec)', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$element->add_control(
			$this->prefix . 'timeline_translate_x',
			[
				'label'       => __( 'Translate X', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add px, vw, %', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$element->add_control(
			$this->prefix . 'timeline_translate_y',
			[
				'label'       => __( 'Translate Y', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add px, vw, %', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$element->add_control(
			$this->prefix . 'timeline_opacity',
			[
				'label'       => __( 'Opacity', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Values between 0-1', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$element->add_control(
			$this->prefix . 'timeline_scale_x',
			[
				'label'       => __( 'Scale X', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add px, vw, %', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$element->add_control(
			$this->prefix . 'timeline_scale_y',
			[
				'label'       => __( 'Scale Y', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add px, vw, %', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$element->add_control(
			$this->prefix . 'timeline_easing',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'Easing', 'gloo_for_elementor' ),
				'options'  => $ease_array,
				'default' => 'Power0.easeNone',
			]
		);

		$element->add_control(
			$this->prefix . 'timeline_rotate',
			[
				'label'       => __( 'Rotate', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Values are in degrees', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$element->add_control(
			$this->prefix . 'timeline_stagger',
			[
				'label'       => __( 'Stagger', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Values are in Seconds', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		
		$element->add_control(
			$this->prefix . 'timeline_custom_arguments',
			[
				'label'       => __( 'Custom Arguments', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'description' => __( 'Example:<br /> fontSize:12px,color:red,fontWeight:bold', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$element->add_control(
			$this->prefix .'timeline_animation_note',
			[
                'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'All Animation Attributes are optional. leave them blank to ignore them', 'gloo_for_elementor' ),
				// 'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
        );
		$element->end_popover(); 












		$element->add_control(
			$this->prefix .'enable_timeline_scrolling',
			[
			  'type' => \Elementor\Controls_Manager::SWITCHER,
			  'label' => __( 'Timeline Scrolling', 'gloo_for_elementor' ),
			  'description' => __( 'Adjust global scrolling parameters to current Timeline.', 'gloo_for_elementor' ),
			  'conditions' => [
				'relation' => 'and',
				'terms' => [
					[
						'name' => $this->prefix .'enable_gsap',
						'operator' => '==',
						'value' => 'yes'
					],
				]
				],
				'separator' => 'before',
			]
		  );

		  $element->add_control(
			$this->prefix .'timeline_scrolling_parameters',
			[
				'label' => __( 'Scrolling Parameters', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				// 'label_off' => __( 'Default', 'gloo_for_elementor' ),
				// 'label_on' => __( 'Custom', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'enable_gsap',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => $this->prefix .'enable_timeline_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);
		$element->start_popover();
		$element->add_control(
			$this->prefix . 'tsp_trigger_element',
			[
				'label'       => __( 'Trigger Element', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( '#id, .class or HTML element. this is the element that will trigger the scroll animation.', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$element->add_control(
			$this->prefix . 'tsp_trigger_start',
			[
				'label'       => __( 'Start', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'top, bottom or %, vh, px - You can also use both like top 83% or bottom 100px or 100px 10%', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$element->add_control(
			$this->prefix . 'tsp_trigger_end',
			[
				'label'       => __( 'End', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'top, bottom or %, vh, px - You can also use both like top 83% or bottom 100px or 100px 10%', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				
			]
		);

		$element->add_control(
			$this->prefix . 'tsp_trigger_scrub',
			[
				'label'       => __( 'Scrub', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Scrub will follow the mouse scrolling, default value is 0 and represents offset in seconds.', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				
			]
		);

		$element->add_control(
			$this->prefix .'tsp_toggle_actions_note',
			[
                'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Toggle Actions', 'gloo_for_elementor' ),
				// 'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				
			]
        );

		
		$element->add_control(
			$this->prefix . 'tsp_action_on_enter',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'onEnter', 'gloo_for_elementor' ),
				'options'  => $actions_array,
				'default' => 'none',
				
			]
		);
		$element->add_control(
			$this->prefix . 'tsp_action_on_leave',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'onLeave', 'gloo_for_elementor' ),
				'options'  => $actions_array,
				'default' => 'none',
				
			]
		);
		$element->add_control(
			$this->prefix . 'tsp_action_on_enter_back',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'onEnterBack', 'gloo_for_elementor' ),
				'options'  => $actions_array,
				'default' => 'none',
				
			]
		);
		$element->add_control(
			$this->prefix . 'tsp_action_on_leave_back',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'onLeaveBack', 'gloo_for_elementor' ),
				'options'  => $actions_array,
				'default' => 'none',
				
			]
		);

		
		$element->add_control(
			$this->prefix . 'tsp_pin_element',
			[
				'label'       => __( 'Pin Element', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( '#id, .class or HTML element. this is the element that will trigger the scroll animation.', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				
			]
		);

		$element->add_control(
			$this->prefix .'tsp_pin_spacing',
			[
			  'type' => \Elementor\Controls_Manager::SWITCHER,
			  'label' => __( 'Pin Spacing', 'gloo_for_elementor' ),
			  'description' => __( 'Auto calculate the spacing required for the pin element.', 'gloo_for_elementor' ),
			  
			]
		  );


		$element->add_control(
			$this->prefix .'tsp_show_markers',
			[
			  'type' => \Elementor\Controls_Manager::SWITCHER,
			  'label' => __( 'Show Markers', 'gloo_for_elementor' ),
			  'description' => __( 'Show Guides of Scroll', 'gloo_for_elementor' ),
			  
			]
		  );

		$element->add_control(
			$this->prefix . 'tsp_start_marker_color',
			[
				'type'     => Controls_Manager::COLOR,
				'label'    => __( 'Start Marker Color', 'gloo_for_elementor' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'tsp_show_markers',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$element->add_control(
			$this->prefix . 'tsp_end_marker_color',
			[
				'type'     => Controls_Manager::COLOR,
				'label'    => __( 'End Marker Color', 'gloo_for_elementor' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'tsp_show_markers',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$element->add_group_control(
			// $this->prefix . 'tsp_font_size',
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => $this->prefix . 'tsp_font_size',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'tsp_show_markers',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
				// 'selector' => '.woocommerce {{WRAPPER}} .price',
			]
		);
		
		
		
		$element->end_popover();  








		$gsap_events_repeater = new Repeater();
		

		$gsap_events_repeater->add_control(
			$this->prefix . 'title',
			[
				'label'       => __( 'Title', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				// 'placeholder' => __( 'Name', 'gloo_for_elementor' ),
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'function',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'Function', 'gloo_for_elementor' ),
				'options'  => [
					'from'      => 'From',
				  	'to'      => 'To',
			  	],
				'default' => 'from',
			]
		);

		
		

		$gsap_events_repeater->add_control(
			$this->prefix .'tween_parameters',
			[
				'label' => __( 'Tween Parameters', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				// 'label_off' => __( 'Default', 'gloo_for_elementor' ),
				// 'label_on' => __( 'Custom', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				// 'conditions' => [
				// 	'relation' => 'and',
				// 	'terms' => [
				// 		[
				// 			'name' => $this->prefix .'enable_gsap',
				// 			'operator' => '==',
				// 			'value' => 'yes'
				// 		],
				// 		[
				// 			'name' => $this->prefix .'enable_timeline',
				// 			'operator' => '==',
				// 			'value' => 'yes'
				// 		],
				// 	]
				// 	],
				
				// 'label_block' => true,
			]
		);
		$gsap_events_repeater->start_popover();
		
		

		$gsap_events_repeater->add_control(
			$this->prefix . 'target',
			[
				'label'       => __( 'Target', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( '#id, .class or HTML element.<br>use commas to separate multiple selectors.', 'gloo_for_elementor' ),
				'placeholder' => __( '#id, .class', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'duration',
			[
				'label'       => __( 'Duration (sec)', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Duration of function (becomes relative if timeline is enabled)', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'delay',
			[
				'label'       => __( 'Delay \ Offset (sec)', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Delay becomes offset when timeline is enabled - it is optional', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'translate_x',
			[
				'label'       => __( 'Translate X', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add px, vw, %', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'translate_y',
			[
				'label'       => __( 'Translate Y', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add px, vw, %', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'opacity',
			[
				'label'       => __( 'Opacity', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Values between 0-1', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'scale_x',
			[
				'label'       => __( 'Scale X', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add px, vw, %', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'scale_y',
			[
				'label'       => __( 'Scale  Y', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add px, vw, %', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'easing',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'Easing', 'gloo_for_elementor' ),
				'options'  => $ease_array,
				'default' => 'Power0.easeNone',
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'rotate',
			[
				'label'       => __( 'Rotate', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Values are in degrees', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'stagger',
			[
				'label'       => __( 'Stagger', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Values are in Seconds', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$gsap_events_repeater->add_control(
			$this->prefix . 'custom_arguments',
			[
				'label'       => __( 'Custom Arguments', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'description' => __( 'Example:<br /> fontSize:12px,color:red,fontWeight:bold', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix .'animation_note',
			[
                'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'All Animation Attributes are optional. leave them blank to ignore them', 'gloo_for_elementor' ),
				// 'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
        );
		$gsap_events_repeater->end_popover(); 

		$gsap_events_repeater->add_control(
			$this->prefix .'event_scrolling',
			[
			  'type' => \Elementor\Controls_Manager::SWITCHER,
			  'label' => __( 'Event Scrolling', 'gloo_for_elementor' ),
			  'description' => __( 'Adjust event scrolling parameters', 'gloo_for_elementor' ),
			//   'conditions' => [
			// 			  'relation' => 'and',
			// 			  'terms' => [
			// 				  [
			// 					  'name' => 'gloo_session_search_string',
			// 					  'operator' => '!=',
			// 					  'value' => 'yes'
			// 				  ],
			// 	  [
			// 					  'name' => 'gloo_session_full_agent_string',
			// 					  'operator' => '!=',
			// 					  'value' => 'yes'
			// 				  ]
			// 			  ]
			// 	]
			]
		  );

		  $gsap_events_repeater->add_control(
			$this->prefix . 'trigger_element',
			[
				'label'       => __( 'Trigger Element', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( '#id, .class or HTML element. this is the element that will trigger the scroll animation.', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'trigger_start',
			[
				'label'       => __( 'Start', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'top, bottom or %, vh, px - You can also use both like top 83% or bottom 100px or 100px 10%', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'trigger_end',
			[
				'label'       => __( 'End', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'top, bottom or %, vh, px - You can also use both like top 83% or bottom 100px or 100px 10%', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'trigger_scrub',
			[
				'label'       => __( 'Scrub', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Scrub will follow the mouse scrolling, default value is 0 and represents offset in seconds.', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix .'toggle_actions_note',
			[
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                // 'type' => \Elementor\Controls_Manager::RAW_HTML,
				'label' => __( 'Toggle Actions', 'gloo_for_elementor' ),
				// 'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
        );

		$gsap_events_repeater->start_popover();
		$gsap_events_repeater->add_control(
			$this->prefix . 'action_on_enter',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'onEnter', 'gloo_for_elementor' ),
				'options'  => $actions_array,
				'default' => 'none',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);
		$gsap_events_repeater->add_control(
			$this->prefix . 'action_on_leave',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'onLeave', 'gloo_for_elementor' ),
				'options'  => $actions_array,
				'default' => 'none',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);
		$gsap_events_repeater->add_control(
			$this->prefix . 'action_on_enter_back',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'onEnterBack', 'gloo_for_elementor' ),
				'options'  => $actions_array,
				'default' => 'none',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);
		$gsap_events_repeater->add_control(
			$this->prefix . 'action_on_leave_back',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => __( 'onLeaveBack', 'gloo_for_elementor' ),
				'options'  => $actions_array,
				'default' => 'none',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);
		$gsap_events_repeater->end_popover(); 

		$gsap_events_repeater->add_control(
			$this->prefix .'show_markers',
			[
			  'type' => \Elementor\Controls_Manager::SWITCHER,
			  'label' => __( 'Show Markers', 'gloo_for_elementor' ),
			  'description' => __( 'Show Guides of Scroll', 'gloo_for_elementor' ),
			  'conditions' => [
				'relation' => 'and',
				'terms' => [
					[
						'name' => $this->prefix .'event_scrolling',
						'operator' => '==',
						'value' => 'yes'
					],
				]
			]
			//   'conditions' => [
			// 			  'relation' => 'and',
			// 			  'terms' => [
			// 				  [
			// 					  'name' => 'gloo_session_search_string',
			// 					  'operator' => '!=',
			// 					  'value' => 'yes'
			// 				  ],
			// 	  [
			// 					  'name' => 'gloo_session_full_agent_string',
			// 					  'operator' => '!=',
			// 					  'value' => 'yes'
			// 				  ]
			// 			  ]
			// 	]
			]
		  );

		$gsap_events_repeater->add_control(
			$this->prefix . 'start_marker_color',
			[
				'type'     => Controls_Manager::COLOR,
				'label'    => __( 'Start Marker Color', 'gloo_for_elementor' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'show_markers',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'end_marker_color',
			[
				'type'     => Controls_Manager::COLOR,
				'label'    => __( 'End Marker Color', 'gloo_for_elementor' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'show_markers',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$gsap_events_repeater->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => $this->prefix . 'font_size',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'show_markers',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
				// 'selector' => '.woocommerce {{WRAPPER}} .price',
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix . 'pin_element',
			[
				'label'       => __( 'Pin Element', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( '#id, .class or HTML element. this is the element that will trigger the scroll animation.', 'gloo_for_elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'event_scrolling',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);

		$gsap_events_repeater->add_control(
			$this->prefix .'pin_spacing',
			[
			  'type' => \Elementor\Controls_Manager::SWITCHER,
			  'label' => __( 'Pin Spacing', 'gloo_for_elementor' ),
			  'description' => __( 'Auto calculate the spacing required for the pin element.', 'gloo_for_elementor' ),
			  'conditions' => [
				'relation' => 'and',
				'terms' => [
					[
						'name' => $this->prefix .'event_scrolling',
						'operator' => '==',
						'value' => 'yes'
					],
				]
			]
			]
		  );

		// $gsap_events_repeater->add_control(
		// 	$this->prefix . 'interactor_variables',
		// 	[
		// 		'type'     => Controls_Manager::SELECT2,
		// 		'label'    => __( 'Variables', 'gloo_for_elementor' ),
		// 		'options'  => $interactor_settings->get_variables(),
		// 		'multiple' => true,
		// 	]
		// );

		$element->add_control(
			$this->prefix,
			[
				'label'         => __( 'Gsap Events', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $gsap_events_repeater->get_controls(),
				'title_field'   => '{{{ ' . $this->prefix . 'title }}}',
				'separator' => 'before',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'enable_gsap',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);


	}

	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    if(!is_admin()){
		
		wp_register_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js', array('jquery'), '3.11.4');
		wp_register_script( 'gsap_scroll_trigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js', array('gsap'), '3.11.4');
		
		$script_abs_path = gloo()->plugin_path( 'includes/modules/interactor_gsap/assets/frontend/js/script.js');
		wp_register_script( $this->prefix.'js', gloo()->plugin_url().'includes/modules/interactor_gsap/assets/frontend/js/script.js', array('gsap_scroll_trigger'), $this->get_file_time($script_abs_path));
		wp_enqueue_script( $this->prefix.'js' );
    }
  }// wp_admin_style_scripts

	public function get_file_time($file){
    	return date("ymd-Gis", filemtime( $file ));
  	}

}
