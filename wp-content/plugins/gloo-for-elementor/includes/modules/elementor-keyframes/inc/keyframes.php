<?php
namespace Gloo\Modules\Elementor_Keyframes;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Keyframes {
	
	private $prefix = 'gloo_';
	/**
	 * Instance
	 *
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Plugin An instance of the class.
	 * @access public
	 *
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @access public
	 */
	public function __construct() {

		// add_action( 'elementor/element/column/layout/after_section_end', [ $this, 'add_keyframes_option' ], 30, 2 );
		// add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'add_keyframes_option' ], 30, 2 );
		
		add_action( 'elementor/element/after_section_end', [ $this, 'add_keyframes_option'], 30, 2);
		
		add_action(	'elementor/element/parse_css', array( $this, 'apply_css_twrapper' ), 10, 2);

	}

	public function apply_css_twrapper($post_css, $element ) {
		/**
		 * @var \Elementor\Post_CSS_File $post_css
		 * @var \Elementor\Element_Base  $element
		 */
		$css_array = array();
		$keyframe_css = '';
		$animation_selector = '';

		$keyframe_activate  = $element->get_settings($this->prefix . 'keyframe_activate');

		if($keyframe_activate == 'yes') {		

			$animations  = $element->get_settings($this->prefix .'animations');
			$animation_duration  = $element->get_settings($this->prefix .'animation_duration');
			$timing_function  = $element->get_settings($this->prefix .'timing_function');
			$animation_delay  = $element->get_settings($this->prefix .'animation_delay');
			
			$animation_is_infinite  = $element->get_settings($this->prefix .'animation_is_infinite');
			$animation_counts  = $element->get_settings($this->prefix .'animation_counts');

			$animation_direction  = $element->get_settings($this->prefix .'animation_direction');
			$animation_state  = $element->get_settings($this->prefix .'animation_state');
			$unique_class  = $element->get_settings($this->prefix .'unique_class');
			$keyframes  = $element->get_settings($this->prefix .'keyframes');
			
			$css_array = array();
			
			if(!empty($animations)) {
				$css_array['animation-name'] = $animations;
			}
			
			if(!empty($animation_duration)) {
				$css_array['animation-duration'] = $animation_duration;
			}
			
			if(!empty($timing_function)) {
				$css_array['animation-timing-function'] = $timing_function;
			}
			
			if(!empty($animation_delay)) {
				$css_array['animation-delay'] = $animation_delay;
			}

			if(!empty($animation_is_infinite) && $animation_is_infinite == 'yes') {
				$css_array['animation-iteration-count'] = 'infinite';
			} else {
				if(!empty($animation_counts)) {
					$css_array['animation-iteration-count'] = $animation_counts;
				}
			}
			
			if(!empty($animation_direction)) {
				$css_array['animation-direction'] = $animation_direction;
			}

			if(!empty($animation_state)) {
				$css_array['animation-play-state'] = $animation_state;
			}
			
			if(!empty($keyframes)) {

				$keyframe_css .= '@keyframes '.$animations.'{';
				foreach($keyframes as $frame) {

					$keyframes_location = $frame[$this->prefix .'keyframes_location'];
					$keyframes_code = $frame[$this->prefix .'keyframes_code'];
					
					if(!empty($keyframes_location)) {
						$keyframes_loc = $keyframes_location['size'].$keyframes_location['unit'];
						$keyframe_css .= str_replace("selector", $keyframes_loc, $keyframes_code);
					}
				}
				$keyframe_css .= '} ';
				
			}

			if(!empty($unique_class)) {
				$animation_selector = '.'.$unique_class;
			} else {
				$animation_selector = $element->get_unique_selector();
			}
		}

		$post_css->get_stylesheet()->add_raw_css($keyframe_css);
		$post_css->get_stylesheet()->add_rules( $animation_selector, $css_array);
	}

	public function add_keyframes_option( $element, $section_id ) {

		if ( 'section_custom_css' !== $section_id ) {
			return;
		}
		   
		$old_section = \ElementorPro\Plugin::elementor()->controls_manager->get_control_from_stack( $element->get_unique_name(), 'section_custom_css' );

		$element->start_controls_section(
			$this->prefix . 'keyframe_section',
			[
				'label' => __( 'Keyframes Animation', 'gloo_for_elementor' ),
				'tab' => $old_section['tab'],
			]
		);

		$element->add_control(
			$this->prefix . 'keyframe_activate',
			[
				'label'        => __( 'Active Keyframe', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Active', 'gloo' ),
				'label_off'    => __( 'off', 'gloo' ),
				'return_value' => 'yes',
				'default'      => 'off',
			]
		);

		$element->add_control(
			$this->prefix .'animations',
			array(
				'label'     => __( 'Animations name', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$element->add_control(
			$this->prefix .'animation_duration',
			array(
				'label'   => __( 'Animation duration', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'placeholder' => '3s',
				'default' => '',
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$timing = array(
			'linear' => 'linear',
			'ease' => 'ease',
			'ease-in-out' => 'ease-in-out',
			'ease-out' => 'ease-out',
			'ease-in' => 'ease-in'
		);

		$element->add_control(
			$this->prefix .'timing_function',
			array(
				'label'   => __( 'Timing function', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'linear',
				'options' => $timing,
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$element->add_control(
			$this->prefix .'animation_delay',
			array(
				'label'   => __( 'Animation delay', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '3s',
				'default' => '',
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$element->add_control(
			$this->prefix .'animation_is_infinite',
			array(
				'label'   => __( 'Iteration Infinite', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo_for_elementor' ),
				'label_off' => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$element->add_control(
			$this->prefix .'animation_counts',
			[
				'label' => __( 'Iteration Count', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix .'animation_is_infinite',
							'operator' => '!=',
							'value' => 'yes'
						],
						[
							'name' => $this->prefix . 'keyframe_activate',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);

		$directions = array(
			'normal' => 'normal',
			'reverse' => 'reverse',
			'alternate' => 'alternate',
			'alternate-reverse' => 'alternate-reverse',
		);

		$element->add_control(
			$this->prefix .'animation_direction',
			array(
				'label'   => __( 'Animation direction', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $directions,
				'default' => 'normal',
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$play_state = array(
			'paused' => 'paused',
			'running' => 'running'
		);

		$element->add_control(
			$this->prefix .'animation_state',
			array(
				'label'   => __( 'Play state', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $play_state,
				'default' => 'running',
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$element->add_control(
			$this->prefix .'unique_class',
			array(
				'label'     => __( 'Unique class(optional)', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			$this->prefix .'keyframes_location', 
			array(
				'label' => __( 'Keyframes Location(%)', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'label_block' => true,	
			)
		);

		$repeater->add_control(
			$this->prefix .'keyframes_code', 
			array(	
				'label' => __( 'Add your own current keyframe css', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::CODE,
				'label_block' => true,
				'language' => 'css',
				'rows' => 20,
				'default' => 'selector {}'
			)
		);

		$element->add_control(
			$this->prefix .'keyframes',
			array(
				'label' => __( 'Keyframes', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'description' => 'USE SELECOR TO EFFECT CURRENT KEY FRAMELOCATION',
				'title_field' => '{{{' . $this->prefix . 'keyframes_location.size}}}%',
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$element->add_control(
			$this->prefix .'keyframes_note',
			array(
				'show_label' => false,
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<p>Use selector to effect current keyframe location<p><br><p>e.g selector { background-color: red; }</p>', 'gloo_for_elementor' ),
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$element->add_control(
			$this->prefix .'keyframes_cache_note',
			array(
				'show_label' => false,
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Keyframe css often get cached by caching plugins, if you don\'t see your changes - please clear cache and try again.', 'gloo_for_elementor' ),
				'condition' => [
					$this->prefix . 'keyframe_activate' => 'yes'
				]
			)
		);

		$element->end_controls_section();

	}

}


// Instantiate Plugin Class
Keyframes::instance();