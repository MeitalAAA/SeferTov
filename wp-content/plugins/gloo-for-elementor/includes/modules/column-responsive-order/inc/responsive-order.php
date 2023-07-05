<?php
namespace Gloo\Modules\Column_Responsive_Order;

/**
 * Main Module file
 */
class Responsive_Order {
	
	private $prefix = 'gloo_cro_';
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

		add_action( 'elementor/element/column/layout/after_section_end', [ $this, 'add_responsive_order_column' ], 30, 2 );
	}

	public function add_responsive_order_column( $element, $section_id ) {

		$element->start_controls_section(
			$this->prefix . 'section',
			[
				'label' => __( 'Column Responsive Order', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_LAYOUT,
			]
		);

		$element->add_responsive_control(
			$this->prefix . 'column_responsive_order',
			[
				'label'        => __( 'Order', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => '0',
				'max' => 12,
				'step' => 1,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'order: {{VALUE}};',
				],
			]
		);

 		$element->end_controls_section();
	}
}


// Instantiate Plugin Class
Responsive_Order::instance();