<?php

namespace Gloo\Modules\PHP_Responsive;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'php_responsive';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {

		$element_types = array(
			'section',
			'column',
			'widget',
		);

		foreach ( $element_types as $el ) {
			add_filter( "elementor/frontend/{$el}/should_render", array( $this, 'check_responsive' ), 10, 2 );
		}
		add_action( 'elementor/element/after_section_end', [ $this, 'register_section' ], 11, 2 );

	}

	public function register_section( $controls_stack, $section_id ) {

		if ( '_section_responsive' !== $section_id ) {
			return;
		}

		$controls_stack->start_controls_section(
			'gloo_responsive_php',
			[
				'label' => __( 'Responsive PHP', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);


		$controls_stack->add_control(
			'gloo_responsive_description',
			[
				'raw'             => __( 'Responsive PHP runs on the server side to determine the device type, unlike the native Responsive controls that use CSS.', 'gloo_for_elementor' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$controls_stack->add_control(
			'gloo_hide_desktop',
			[
				'label'        => __( 'Hide On Desktop', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'prefix_class' => 'elementor-',
				'label_on'     => 'Hide',
				'label_off'    => 'Show',
				'return_value' => 'hidden-desktop',
			]
		);

		$controls_stack->add_control(
			'gloo_hide_tablet',
			[
				'label'        => __( 'Hide On Tablet', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'prefix_class' => 'elementor-',
				'label_on'     => 'Hide',
				'label_off'    => 'Show',
				'return_value' => 'hidden-tablet',
			]
		);

		$controls_stack->add_control(
			'gloo_hide_mobile',
			[
				'label'        => __( 'Hide On Mobile', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'prefix_class' => 'elementor-',
				'label_on'     => 'Hide',
				'label_off'    => 'Show',
				'return_value' => 'hidden-phone',
			]
		);


		$controls_stack->end_controls_section();
	}

	public function check_responsive( $result, $element ) {

		if ( ! class_exists( 'Mobile_Detect' ) ) {
			require gloo()->plugin_path( 'includes/lib/mobiledetect/mobile_detect.php' );
		}
		$settings = $element->get_settings();

		$hide_desktop = $settings['gloo_hide_desktop'];
		$hide_tablet  = $settings['gloo_hide_tablet'];
		$hide_mobile  = $settings['gloo_hide_mobile'];

		// no settings
		if ( ! $hide_desktop && ! $hide_mobile && ! $hide_mobile ) {
			return $result;
		}

		$detect = new \Mobile_Detect;

		// Mobile
		if ( $detect->isMobile() && ! $detect->isTablet() ) {
			if ( $hide_mobile ) {
				return false;
			}
		}

		// Tablet
		if ( $detect->isTablet() ) {
			if ( $hide_tablet ) {
				return false;
			}
		}

		// Desktop
		if ( ! $detect->isMobile() && ! $detect->isTablet() ) {

			if ( $hide_desktop ) {
				return false;
			}
		}

		return $result;

	}

	/**
	 * Returns the instance.
	 *
	 * @return Module
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
