<?php
namespace Gloo\Modules\Elementor_Clickable;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Clickable {
	
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

		add_action( 'wp_enqueue_scripts', array( $this, 'clickable_frontend_scripts' ) );

		add_action( 'elementor/element/column/layout/after_section_end', [ $this, 'add_clickable_option' ], 30, 2 );
		add_action( 'elementor/element/section/section_layout/after_section_end', [ $this, 'add_clickable_option' ], 10, 2 );
		
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render_clickable' ), 10 );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render_clickable' ), 10 );

	}

	public function add_clickable_option( $element, $section_id ) {

		$element->start_controls_section(
			$this->prefix . 'section',
			[
				'label' => __( 'Clickable+', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_LAYOUT,
			]
		);

		$element->add_control(
			$this->prefix . 'clickable_activate',
			[
				'label'        => __( 'Active Clickable', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Active', 'gloo' ),
				'label_off'    => __( 'off', 'gloo' ),
				'return_value' => 'yes',
				'default'      => 'off',
			]
		);

		$element->add_control(
			$this->prefix .'link',
			[
				'label' => __( 'Link', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'gloo_for_elementor' ),
				'show_external' => true,
				'condition' => [
					$this->prefix . 'clickable_activate' => 'yes'
				],
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				],
			]
		);

		$element->end_controls_section();

	}

    public function before_render_clickable( $element ) {
		$settings = $element->get_settings_for_display();

		$clickable_link = (isset($settings['gloo_link']['url']) && !empty($settings['gloo_link']['url']))? $settings['gloo_link']: '';
		$clickable_activate = (isset($settings['gloo_clickable_activate']) && $settings['gloo_clickable_activate'] == 'yes')? $settings['gloo_clickable_activate']: '';
		
		if($clickable_activate == 'yes' && ! empty($clickable_link['url'])) {
 				
			wp_enqueue_script( 'gloo-clickable' );

			$element->add_render_attribute( '_wrapper', 'class', 'gloo-clickable-item' );
			$element->add_render_attribute( '_wrapper', 'style', 'cursor: pointer;' );
			$element->add_render_attribute( '_wrapper', 'data-gloo-item-clickable', $clickable_link['url'] );
			$element->add_render_attribute( '_wrapper', 'data-gloo-item-clickable-blank', $clickable_link['is_external'] ? '_blank' : '_self' );
			
		}
	}

	public function clickable_frontend_scripts() {
		wp_register_script( 'gloo-clickable', gloo()->plugin_url( 'assets/js/admin/gloo-clickable.js' ), [ 'jquery' ], gloo()->get_version() );
	}

}


// Instantiate Plugin Class
Clickable::instance();