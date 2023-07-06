<?php
namespace Gloo\Modules\WooCommerce_Dynamic_Tags_Kit;

class Virtual_Products extends \Elementor\Core\DynamicTags\Data_Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'gloo-wdt-virtual-products';
	}

	/**
	 * Get Title
	 *
	 * Returns the title of the Tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Products by Virtual Status', 'gloo_for_elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @return array
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_categories() {
		return [
			\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY
		];
	}

	/**
	 * Register Controls
	 *
	 * Registers the Dynamic tag controls
	 *
	 * @return void
	 * @since 2.0.0
	 * @access protected
	 *
	 */
	protected function _register_controls() {

		$this->add_control(
			'status',
			array(
				'label'   => __( 'Virtual Status', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_none',
				'options' => [
					'no'     => 'No',
					'yes'    => 'Yes',
				],
			)
		);

		$this->add_control(
			'output',
			array(
				'label'   => __( 'Output', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'array',
				'options' => [
					'delimiter' => 'Delimited String',
					'array'     => 'Array',
				],
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label' => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'output' => 'delimiter'
				],
			)
		);

	}

	public function get_value( array $options = array() ) {

		$status = $this->get_settings( 'status' );
		$output = $this->get_settings( 'output' );

		if ( ! $status ) {
			return;
		}

		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => 'product',
			'hide_empty'     => 1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_virtual',
					'value'   => $status,
					'compare' => '=',
				)
			)
		);

		$results = ( new \WP_Query( $args ) )->posts;

		if ( $output === 'delimiter' ) {
			$delimiter = $this->get_settings( 'delimiter' ) ? $this->get_settings( 'delimiter' ) : ',';
			$results   = implode( $delimiter, $results );
		}

		return $results;

	}

}