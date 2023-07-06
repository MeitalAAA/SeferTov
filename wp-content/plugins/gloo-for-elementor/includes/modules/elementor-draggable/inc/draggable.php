<?php
namespace Gloo\Modules\Elementor_Draggable;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Draggable {
	
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

		add_action( 'wp_enqueue_scripts', array( $this, 'draggable_frontend_scripts' ) );

		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'add_draggable_option' ], 30, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'add_draggable_option' ], 30, 2 );

		add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render_draggable' ), 10 );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render_draggable' ), 10 );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render_draggable' ), 10 );
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'before_render_draggable' ), 10 );

	}

	public function add_draggable_option( $element, $section_id ) {
	
		   
		$element->start_controls_section(
			$this->prefix . 'draggable',
			[
				'label' => __( 'Draggable+', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			$this->prefix . 'draggable_activate',
			[
				'label'        => __( 'Active draggable', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Active', 'gloo_for_elementor' ),
				'label_off'    => __( 'off', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'off',
			]
		);

		$element->end_controls_section();

	}

    public function before_render_draggable( $element ) {
		
		$draggable_activate = $element->get_settings($this->prefix . 'draggable_activate');

		if($draggable_activate == 'yes') {

			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'gloo-draggable' );

			$element->add_render_attribute( '_wrapper', 'class', 'gloo-draggable-item' );
			$element->add_render_attribute( '_wrapper', 'style', 'cursor: move;position: absolute;z-index: 9;' );
		} 
	}

	public function draggable_frontend_scripts() {
		wp_register_script( 'gloo-draggable', gloo()->plugin_url( 'assets/js/admin/gloo-draggable.js' ), [ 'jquery' ], gloo()->get_version() );
	}

}

// Instantiate Plugin Class
Draggable::instance();