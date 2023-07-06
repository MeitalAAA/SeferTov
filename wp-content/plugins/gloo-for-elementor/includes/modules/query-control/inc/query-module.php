<?php
namespace Gloo\Modules\Query_Control;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Module;
use ElementorPro\Modules\QueryControl\Module as Module_Query;
use ElementorPro\Modules\Woocommerce\Classes\Products_Renderer;
use ElementorPro\Modules\Woocommerce\Classes\Current_Query_Renderer;

class Query_Module extends Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	private $prefix = 'gloo_qc_';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'elementor/element/posts/section_query/before_section_end', [ $this, 'query_control_options' ], 10, 2 );
		add_action( 'elementor/element/portfolio/section_query/before_section_end', [ $this, 'query_control_options' ], 10, 2 );
		add_action( 'elementor/element/loop-grid/section_query/before_section_end', [ $this, 'query_control_options' ], 10, 2 );
		add_filter( 'elementor/query/query_args', [ $this, 'query_control_query_args' ], 10, 2 );

		add_action( 'elementor/element/posts/section_query/after_section_end', [ $this, 'query_update_control_options' ], 10, 2 );
		add_action( 'elementor/element/portfolio/section_query/after_section_end', [ $this, 'query_update_control_options' ], 10, 2 );
	}

	public function get_name() {
		return 'gloo_post_queries';
	}

	public function query_update_control_options( $element, $args ) {
		$elementor = \Elementor\Plugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack( $element->get_name(), 'posts_orderby' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$order_by = [
			'ID' => 'ID',
			'menu_order' => 'Menu Order',
			'name' => 'Slug'
		];

		$control_data['options'] = array_merge($control_data['options'], $order_by);

		$element->update_control('posts_orderby', $control_data);

	}

	public function query_control_options( $element, $args ) {
		 
		$name = $element->get_name();
		$name .= '_';

		$element->add_control(
			$this->prefix . 'query_options',
			[
				'label'     => __( 'Query Options', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SELECT,
				'separator' => 'before',
				'default' => '',
				'options' => [
					'posts'  => __( 'Posts', 'gloo_for_elementor' ),
					'terms'  => __( 'Terms', 'gloo_for_elementor' ),
				],
			]
		);

		/* fields for posts */
		$element->add_control(
			$this->prefix . 'posts_exclude',
			[
				'label' => __( 'Exclude Posts', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					$this->prefix . 'query_options' => 'posts'
				],
			],
		);

		$element->add_control(
			$this->prefix . 'posts_include',
			[
				'label' => __( 'Include Posts', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					$this->prefix . 'query_options' => 'posts'
				],
			],
		);

		/* fields for terms */

		$labels = [];
		$tax_args = [
			'public' => true,
		];

		$taxonomies = get_taxonomies($tax_args);

		if(!empty($taxonomies)) {
			foreach ($taxonomies as $tax) {
				$tax_info = get_taxonomy($tax);
				$labels[$tax] = $tax_info->label;
			}

		}

		$element->add_control(
			$this->prefix . 'taxonomy',
			array(
				'label'   => __( 'Select Taxonomy', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $labels,
				'condition' => [
					$this->prefix . 'query_options' => 'terms'
				],
			)
		);

		$element->add_control(
			$this->prefix . 'terms_exclude',
			[
				'label' => __( 'Exclude Terms', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					$this->prefix . 'query_options' => 'terms'
				],
			],
		);

		$element->add_control(
			$this->prefix . 'terms_include',
			[
				'label' => __( 'Include Terms', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					$this->prefix . 'query_options' => 'terms'
				],
			],
		);

		$element->add_control(
			$this->prefix . 'info',
			[
				'label' => __( 'Field Info', 'gloo_for_elementor' ),
				'show_label' => false,
				'raw' => __( 'Include - Exclude field require comma separated ids of Posts, Terms, User' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
       			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);
		
	}

	public function query_control_query_args( $query_vars, $widget ) {
		$settings = $widget->get_settings_for_display();
		 

		if( isset( $settings[$this->prefix . 'query_options'] ) && !empty( $settings[$this->prefix . 'query_options'] ) ) {

			/* post query args */
			if( $settings[$this->prefix . 'query_options'] == 'posts' ) {
				if( isset( $settings[$this->prefix . 'posts_include'] ) && !empty( $settings[$this->prefix . 'posts_include'] ) ) {

					$included = explode(',', $settings[$this->prefix . 'posts_include']);
					
					if( is_array( $included ) && !empty( $included ) ) {
						
						if( empty( $query_vars[ 'post__not_in' ]) ) {
							$query_vars[ 'post__in' ] = [];
						}

						$query_vars[ 'post__in' ] = array_merge( $query_vars[ 'post__in' ], $included );
					}
				}

				if( isset( $settings[$this->prefix . 'posts_exclude'] ) && !empty( $settings[$this->prefix . 'posts_exclude'] ) ) {

					$excluded = explode(',', $settings[$this->prefix . 'posts_exclude']);
					
					if( is_array( $excluded ) && !empty( $excluded ) ) {

						if( empty( $query_vars[ 'post__not_in' ]) ) {
							$query_vars[ 'post__not_in' ] = [];
						}
						
						$query_vars[ 'post__not_in' ] = array_merge( $query_vars[ 'post__not_in' ], $excluded);
					}
				}
			/* terms query */	
			} elseif( $settings[$this->prefix . 'query_options'] == 'terms' ) {
				
				if( isset( $settings[$this->prefix . 'taxonomy'] ) && !empty( $settings[$this->prefix . 'taxonomy'] ) ) {
					
					$taxonomy = $settings[$this->prefix . 'taxonomy'];

					if( isset( $settings[$this->prefix . 'terms_include'] ) && !empty( $settings[$this->prefix . 'terms_include'] ) ) {
						$included_terms = explode(',', $settings[$this->prefix . 'terms_include']);
						
						if( is_array( $included_terms ) && !empty( $included_terms ) ) { 

							$query_vars[ 'tax_query' ][] = [
								'taxonomy' => $taxonomy,
								'field' => 'id',
								'terms' => $included_terms,
								'operator' => 'IN'
							];
						}
					}

					if( isset( $settings[$this->prefix . 'terms_exclude'] ) && !empty( $settings[$this->prefix . 'terms_exclude'] ) ) {
						$excluded_terms = explode(',', $settings[$this->prefix . 'terms_exclude']);
					
						if( is_array( $excluded_terms ) && !empty( $excluded_terms ) ) { 

							$query_vars[ 'tax_query' ][] = [
								'taxonomy' => $taxonomy,
								'field' => 'id',
								'terms' => $excluded_terms,
								'operator' => 'NOT IN'
							];
						}
					}
				}
			}
		 }
		
		return $query_vars;
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
Query_Module::instance(); 