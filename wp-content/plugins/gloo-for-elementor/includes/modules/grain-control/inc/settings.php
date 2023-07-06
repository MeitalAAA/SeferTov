<?php
namespace Gloo\Modules\Grain_Control;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Grainable {
	
	private $prefix = 'gloo_grain';
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

		
		add_action( 'wp_enqueue_scripts', array( $this, 'clickable_frontend_scripts' ) );

		add_action( 'elementor/element/column/layout/after_section_end', [ $this, 'add_clickable_option' ], 30, 2 );
		add_action( 'elementor/element/section/section_layout/after_section_end', [ $this, 'add_clickable_option' ], 10, 2 );
		
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render_grainable' ), 10 );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render_grainable' ), 10 );

		add_action( 'elementor/element/before_render', [ $this, 'before_render_grainable'], 30, 2);
	}

	public function add_clickable_option( $element, $section_id ) {

		$element->start_controls_section(
			$this->prefix . 'section',
			[
				'label' => __( 'Grain Control', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_LAYOUT,
			]
		);

		$element->add_control(
			'gloo_responsive_description',
			[
				'raw'             => __( 'view preview of the page or live page for changes , it will not work in editor side', 'gloo_for_elementor' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$element->add_control(
			$this->prefix . '_activate',
			[
				'label'        => __( 'Active Grainer', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Active', 'gloo' ),
				'label_off'    => __( 'off', 'gloo' ),
				'return_value' => 'yes',
				'default'      => 'off',
			]
		);

		$element->add_control(
			$this->prefix .'_id',
			[
				'label' => __( 'unique ID(Required)', 'gloo_for_elementor'),
				'description'=>__('A unique ID is required to initilize Grainer; without an ID, Grainer cannot initilize.'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Unique ID for the container', 'gloo_for_elementor' ),
				'condition' => [
					$this->prefix . '_activate' => 'yes'
				],
			]
		);

		$element->add_control(
			$this->prefix . '_animate',
			[
				'label'        => __( 'Animate', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Animate', 'gloo' ),
				'label_off'    => __( 'off', 'gloo' ),
				'return_value' => 'yes',
				'default'      => 'off',
				'condition' => [
					$this->prefix . '_activate' => 'yes'
				],
			]
		);

		
		$element->add_control(
			$this->prefix . '_patternWidth',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Pattern Width', 'plugin-name' ),
				'min' => 0,
				'max' => 500,
				'default' => 100,
				'step' => 10,
				'condition' => [
					$this->prefix . '_activate' => 'yes'
				],
				
			]
		);

		$element->add_control(
			$this->prefix . '_patternHeight',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Pattern Height', 'plugin-name' ),
				'min' => 0,
				'max' => 500,
				'default' => 100,
				'step' => 10,
				'condition' => [
					$this->prefix . '_activate' => 'yes'
				],
				
			]
		);

		$element->add_control(
			$this->prefix . '_grainDensity',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Grain Density', 'plugin-name' ),
				'min' => 0,
				'max' => 10,
				'default' => 10,
				'condition' => [
					$this->prefix . '_activate' => 'yes'
				],
				
			]
		);
		$element->add_control(
			$this->prefix . '_grainOpacity',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Grain Opacity', 'plugin-name' ),
				'min' => 0,
				'max' => 1,
				'default' =>1,
				  'step'=>0.1,
				  'condition' => [
					$this->prefix . '_activate' => 'yes'
				],
				
			]
		);	
		$element->add_control(
			$this->prefix . '_grainWidth',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Grain Width', 'plugin-name' ),
				'min' => 0,
				'max' => 10,
				'default' => 10,
				'condition' => [
					$this->prefix . '_activate' => 'yes'
				],
				
			]
		);

		
		$element->add_control(
			$this->prefix . '_grainHeight',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Grain Height', 'plugin-name' ),
				'min' => 0,
				'max' => 10,
				'default' => 10,
				'condition' => [
					$this->prefix . '_activate' => 'yes'
				],
				
			]
		);
		$element->end_controls_section();
	}
    public function before_render_grainable( $element ) {
		$settings = $element->get_settings_for_display();
		$section_id = (isset($settings['gloo_grain_id']) && ($settings['gloo_grain_id']) != "") ?  $settings['gloo_grain_id']: '';
		$clickable_activate = (isset($settings['gloo_grain_activate']) && $settings['gloo_grain_activate'] == 'yes')? $settings['gloo_grain_activate']: '';
		$is_animate = (isset($settings['gloo_grain_animate']) && $settings['gloo_grain_animate'] == 'yes')? 'true' : 'false';
		$gloo_grain_patternWidth = (isset($settings['gloo_grain_patternWidth']) && $settings['gloo_grain_patternWidth'] != '')? $settings['gloo_grain_patternWidth']: 100;
		$gloo_grain_patternHeight = (isset($settings['gloo_grain_patternHeight']) && $settings['gloo_grain_patternHeight'] != '')? $settings['gloo_grain_patternHeight']: 100;
		$gloo_grain_grainOpacity = (isset($settings['gloo_grain_grainOpacity']) && $settings['gloo_grain_grainOpacity'] != '')? $settings['gloo_grain_grainOpacity']: '0.0.5';
		$gloo_grain_grainDensity = (isset($settings['gloo_grain_grainDensity']) && $settings['gloo_grain_grainDensity'] != '')? $settings['gloo_grain_grainDensity']: 1;
		$gloo_grain_grainWidth = (isset($settings['gloo_grain_grainWidth']) && $settings['gloo_grain_grainWidth'] != '')? $settings['gloo_grain_grainWidth']: 1;
		$gloo_grain_grainHeight = (isset($settings['gloo_grain_grainHeight']) && $settings['gloo_grain_grainHeight'] != '')? $settings['gloo_grain_grainHeight']: 1;	
		
		if($clickable_activate == 'yes' && $section_id != "") {	
			wp_enqueue_script( 'gloo-grain' );
			$element->add_render_attribute( '_wrapper', 'id', $section_id );
			  echo '<script>
			  jQuery(function($) {
			  var options = {
				"animate": '.$is_animate.',
				"patternWidth": '.$gloo_grain_patternWidth.',
				"patternHeight": '.$gloo_grain_patternHeight.',
				"grainOpacity": '.$gloo_grain_grainOpacity.',
				"grainDensity": '.$gloo_grain_grainDensity.',
				"grainWidth": '.$gloo_grain_grainWidth.',
				"grainHeight": '.$gloo_grain_grainHeight.'
			  }
			    grained("#'.$section_id .'", options);
				console.log( "ready!" );
			});
			  </script>'  ;	     	
	}
	}
	public function clickable_frontend_scripts() {
		wp_register_script( 'gloo-grain', 'https://cdn.jsdelivr.net/gh/sarathsaleem/grained@master/grained.js', [ 'jquery' ], gloo()->get_version() );
	}
}
new Grainable();

// Instantiate Plugin Class
