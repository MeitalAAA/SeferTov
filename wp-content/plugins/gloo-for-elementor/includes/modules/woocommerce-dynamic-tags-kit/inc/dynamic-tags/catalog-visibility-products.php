<?php

namespace Gloo\Modules\WooCommerce_Dynamic_Tags_Kit;

class Catalog_Visibility_Products extends \Elementor\Core\DynamicTags\Data_Tag {

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
		return 'gloo-wdt-visibility-products';
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
		return __( 'Products by Catalog Visibility', 'gloo_for_elementor' );
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
				'label'   => __( 'Catalog Visibility', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_none',
				'options' => [
					'both'                 => 'Shop and search results',
					'exclude-from-search'  => 'Shop only',
					'exclude-from-catalog' => 'Search results only',
					'none'                 => 'Hidden',
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
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
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
			'taxonomy' => 'product_visibility',
			'terms'    => [ $status ],
			'field'    => 'name',
			'operator' => 'IN',
		);

		if ( $status === 'both' ) {
			$args['terms']    = [ 'exclude-from-search', 'exclude-from-catalog' ];
			$args['operator'] = 'NOT IN';

		}

		if ( $status === 'none' ) {
			$args['terms']    = [ 'exclude-from-search', 'exclude-from-catalog' ];
			$args['operator'] = 'AND';
		}

		$results = ( new \WP_Query( array(
			'posts_per_page' => - 1,
			'post_type'      => 'product',
			'hide_empty'     => 1,
			'fields'         => 'ids',
			'tax_query'      => array( $args )
		) ) )->posts;

		if ( $output === 'delimiter' ) {
			$delimiter = $this->get_settings( 'delimiter' ) ? $this->get_settings( 'delimiter' ) : ',';
			$results   = implode( $delimiter, $results );
		}

		return $results;

	}

}