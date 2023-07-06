<?php

namespace Gloo\Modules\Dynamify_Repeaters;

use ElementorPro\Modules\QueryControl\Module as Query_Module;

class Dynamify_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

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
		return 'gloo-dynamify-tag';
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
		return __( 'Dynamify Tag', 'gloo_for_elementor' );
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
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::NUMBER_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::COLOR_CATEGORY,
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
	protected function register_controls() {

		$this->add_control(
			'source',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Repeater Source', 'gloo_for_elementor' ),
				'label_block' => true,
				'default'     => 'acf',
				'classes'     => 'gloo-hidden-control',
				'options'     => [],
				'condition'   => [
					'follow_mode!' => 'yes',
				],
			)
		);

		$this->add_control(
			'context',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Context', 'gloo_for_elementor' ),
				'label_block' => true,
				'default'     => 'post',
				'classes'     => 'gloo-hidden-control',
				'options'     => array(
					'post'         => __( 'Post', 'gloo_for_elementor' ),
					'user'         => __( 'User', 'gloo_for_elementor' ),
					'term'         => __( 'Term', 'gloo_for_elementor' ),
					'options_page' => __( 'Options Page', 'gloo_for_elementor' ),
				),
				'condition'   => [
					'follow_mode!' => 'yes',
				],
			)
		);

		$this->add_control(
			'post',
			array(
				'label'          => __( 'Post', 'gloo_for_elementor' ),
				'label_block'    => true,
				'classes'     => 'gloo-hidden-control',
				'type'           => Query_Module::QUERY_CONTROL_ID,
				'autocomplete'   => [
					'object'  => Query_Module::QUERY_OBJECT_POST,
					'display' => 'post_type_prefix',
				],
				'select2options' => [
					'placeholder' => 'Default: Current Post',
				],
				'condition'      => [
					'source'  => [ 'jet_engine', 'acf' ],
					'context' => 'post',
				]
			)
		);

		$this->add_control(
			'term',
			array(
				'label'          => __( 'Term', 'gloo_for_elementor' ),
				'label_block'    => true,
				'classes'     => 'gloo-hidden-control',
				'type'           => Query_Module::QUERY_CONTROL_ID,
				'autocomplete'   => [
					'object'  => Query_Module::QUERY_OBJECT_CPT_TAX,
					'display' => 'detailed',
				],
				'select2options' => [
					'placeholder' => 'Default: Current Term',
				],
				'description'    => __( 'Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'gloo_for_elementor' ),
				'condition'      => [
					'source'  => [ 'jet_engine', 'acf' ],
					'context' => 'term',
				]
			)
		);

		$this->add_control(
			'user',
			array(
				'label'          => __( 'User', 'gloo_for_elementor' ),
				'label_block'    => true,
				'classes'     => 'gloo-hidden-control',
				'type'           => Query_Module::QUERY_CONTROL_ID,
				'autocomplete'   => [
					'object' => Query_Module::QUERY_OBJECT_USER,
				],
				'select2options' => [
					'placeholder' => 'Default: Current User',
				],
				'condition'      => [
					'source'  => [ 'jet_engine', 'acf' ],
					'context' => 'user',
				]
			)
		);

		if ( function_exists( 'jet_engine' ) ) {
			$this->add_control(
				'jet_options_page_field',
				array(
					'label'     => __( 'Repeater Field', 'gloo_for_elementor' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'default'   => '',
					'classes'   => 'gloo-hidden-control',
					'groups'    => [],
					'condition' => [
						'context' => 'options_page',
						'source'  => 'jet_engine',
					],
				)
			);
		}

		$this->add_control(
			'field',
			array(
				'label'      => __( 'Repeater Field', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'classes'    => 'gloo-hidden-control',
				'dynamic'    => [
					'active' => true,
				],
				'conditions' => [

					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'context',
									'operator' => '!==',
									'value'    => 'options_page'
								],
								[
									'name'     => 'source',
									'operator' => '!==',
									'value'    => 'jet_engine'
								]
							]
						],
						[
							'name'     => 'follow_mode',
							'operator' => '!==',
							'value'    => 'yes'
						]
					]
				],
			)
		);

		$this->add_control(
			'subfield',
			array(
				'label'   => __( 'Repeater Subfield', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],

			)
		);


		$this->add_control(
			'index',
			array(
				'label'       => __( 'Array Index', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => '0',
				'classes'     => 'gloo-hidden-control',
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'follow_mode!' => 'yes'
				],
			)
		);

		$this->add_control(
			'follow_mode',
			[
				'label'        => __( 'Follow Listing Mode', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::HIDDEN,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
			]
		);


		$this->add_control(
			'collective_mode_follow_dynamify',
			array(
				'label'        => __( 'Collective Output', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'description'  => 'Returns a collection of subfield values from all repeater items.',
				'condition'    => [
					'follow_mode' => 'yes'
				]
			)
		);
		
		$this->add_control(
			'delimiter_follow_dynamify',
			array(
				'label'       => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => ',',
				'condition'   => [
					'collective_mode_follow_dynamify'   => 'yes',
					'follow_mode' => 'yes'
				],
			)
		);
		
		$this->add_control(
			'index_follow_dynamify',
			array(
				'label'       => __( 'Array Index', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => '0',
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'collective_mode_follow_dynamify!' => 'yes',
					'follow_mode' => 'yes'
				],
			)
		);


	}

	/**
	 * Render
	 *
	 * Prints out the value of the Dynamic tag
	 *
	 * @return array|string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_value( array $options = array() ) {

		$source   = $this->get_settings_for_display( 'source' );
		$context  = $this->get_settings_for_display( 'context' );
		$field    = $this->get_settings_for_display( 'field' );
		$subfield = $this->get_settings_for_display( 'subfield' );
		$index    = intval( $this->get_settings_for_display( 'index' ) );

		if ( $source === 'jet_engine' && $context === 'options_page' ) {
			$field = $this->get_settings_for_display( 'jet_options_page_field' );
		}

		switch ( $source ) {
			case 'acf':
				$result = $this->acf_get_data( $field, $subfield, $index, $context );
				break;
			case 'jet_engine':
				$result = $this->jet_engine_get_data( $field, $subfield, $index, $context );
				break;
			default:
				$result = apply_filters( 'gloo/modules/repeater_dynamic_tag/output', '', $this->get_settings_for_display() );
		}

		if ( empty( $result ) && $this->get_settings( 'fallback' ) ) {
			$result = $this->get_settings( 'fallback' );
		}

		if((is_array($result) && count($result) >= 1)){
			$collective_mode_follow = $this->get_settings_for_display( 'collective_mode_follow_dynamify' );
			if($collective_mode_follow == 'yes'){
				
				$delimiter_follow = $this->get_settings_for_display( 'delimiter_follow_dynamify' );
				if(empty($delimiter_follow))
					$delimiter_follow = ', ';
				return implode($delimiter_follow, $result);
			}else{
				$index_follow           = intval( $this->get_settings_for_display( 'index_follow_dynamify' ) );			 
			if($index_follow && isset($result[$index_follow]))
					return $result[$index_follow];
				elseif(isset($result[0]))
					return $result[0];
				else
					return reset($result);
			}
		}else
			return $result;
	}

	public function get_post_id() {
		$post_id = \Elementor\Plugin::instance()->editor->get_post_id();

		$current_listing = jet_engine()->listings->data->get_listing();
		if ( method_exists( $current_listing, 'get_id' ) && ! $post_id ) {
			$post_id = $current_listing->get_id();
		}

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		return $post_id;
	}

	public function is_listing_source_repeater() {
		if ( ! function_exists( 'jet_engine' ) ) {
			return;
		}


		$post_id = $this->get_post_id();


		$settings = get_post_meta( $post_id, '_elementor_page_settings', true );

		return $settings && isset( $settings['listing_source'] ) && $settings['listing_source'] === 'repeater';
	}


	public function get_listing_repeater() {
		$post_id = $this->get_post_id();

		$settings = get_post_meta( $post_id, '_elementor_page_settings', true );


		return [
			'repeater_source' => isset( $settings['repeater_source'] ) ? $settings['repeater_source'] : 'jet_engine',
			'repeater_option' => isset( $settings['repeater_option'] ) ? $settings['repeater_option'] : false,
			'repeater_field'  => isset( $settings['repeater_field'] ) ? $settings['repeater_field'] : false,
		];
	}


	public function jet_engine_get_data( $field, $subfield, $index, $context ) {
		if ( ! function_exists( 'jet_engine' ) ) {
			return;
		}

		switch ( $context ) {
			case 'options_page' :
				$items = jet_engine()->listings->data->get_option( $field );
				break;
			case 'term' :
				$term_id = $this->get_settings( "term" ) ? $this->get_settings( "term" ) : get_queried_object_id();
				$items   = $items = get_term_meta( $term_id, $field, true );
				break;
			case 'user' :
				$user_id = $this->get_settings( "user" ) ? $this->get_settings( "user" ) : get_current_user_id();
				$items   = $items = get_user_meta( $user_id, $field, true );
				break;
			default : // current_post
				$post_id = $this->get_settings( "post" ) ? $this->get_settings( "post" ) : get_the_ID();
				$items   = get_post_meta( $post_id, $field, true );
		}

		if ( ! $items ) { // no items
			return;
		}

		$index = "item-" . $index;
		if ( ! isset( $items[ $index ] ) ) {
			return;
		}

		return isset( $items[ $index ][ $subfield ] ) ? $items[ $index ][ $subfield ] : null;
	}

	public function acf_get_data( $field, $subfield, $index, $context ) {
		if ( ! function_exists( 'acf' ) ) {
			return;
		}

		switch ( $context ) {
			case 'options_page' :
				$items = get_field( $field, 'option' );
				break;
			case 'term' :
				$term_id = $this->get_settings( "term" ) ? $this->get_settings( "term" ) : get_queried_object_id();
				$term    = get_term( $term_id );
				$items   = get_field( $field, "{$term->taxonomy}_{$term_id}" );
				break;
			case 'user' :
				$user_id = $this->get_settings( "user" ) ? $this->get_settings( "user" ) : get_queried_object_id();
				$items   = get_field( $field, "user_{$user_id}" );
				break;
			default : // current_post
				$post_id = $this->get_settings( "post" ) ? $this->get_settings( "post" ) : get_the_ID();
				$items   = get_field( $field, $post_id );
		}

		if ( ! $items ) { // no items
			return;
		}

		if ( ! isset( $items[ $index ] ) ) {
			return;
		}

		return isset( $items[ $index ][ $subfield ] ) ? $items[ $index ][ $subfield ] : null;
	}
}