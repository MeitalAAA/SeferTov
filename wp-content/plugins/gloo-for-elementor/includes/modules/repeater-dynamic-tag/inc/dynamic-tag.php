<?php

namespace Gloo\Modules\Repeater_Dynamic_Tag;

use ElementorPro\Modules\QueryControl\Module as Query_Module;

class Repeater_Dynamic_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

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
		return 'gloo-repeater-tag';
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
		return __( 'Repeater Tag', 'gloo_for_elementor' );
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

		$sources = [];

		if ( function_exists( 'jet_engine' ) ) {
			$sources['jet_engine'] = __( 'JetEngine', 'gloo_for_elementor' );
		}


		if ( function_exists( 'acf' ) ) {
			$sources['acf'] = __( 'ACF', 'gloo_for_elementor' );
		}

		$sources = apply_filters( 'gloo/modules/repeater_dynamic_tag/sources', $sources );

		$this->add_control(
			'source',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Repeater Source', 'gloo_for_elementor' ),
				'label_block' => true,
				'default'     => 'acf',
				'options'     => $sources,
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
				'default'     => 'current_post',
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
				'type'           => Query_Module::QUERY_CONTROL_ID,
				'autocomplete'   => [
					'object'  => Query_Module::QUERY_OBJECT_CPT_TAX,
					'display' => 'detailed',
				],
				'select2options' => [
					'placeholder' => 'Default: Current Term',
				],
				'description'    => __( 'Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'gloo' ),
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

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'repeater' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'jet_options_page_field',
					array(
						'label'     => __( 'Repeater Field', 'gloo_for_elementor' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => [
							'context'      => 'options_page',
							'source'       => 'jet_engine',
							'follow_mode!' => 'yes',
						],
					)
				);
			}
		}

		$is_listing_source_repeater = $this->is_listing_source_repeater();

		if ( $is_listing_source_repeater ) {
			$repeater_data = $this->get_listing_repeater();

			$repeater_field = $repeater_data['repeater_field'];
			if ( isset( $repeater_data['repeater_option'] ) && $repeater_data['repeater_option'] ) {
				$repeater_field = str_replace( '::', ' > ', $repeater_data['repeater_option'] );
			}
			$this->add_control(
				'field_pre_set',
				array(
					'label'     => __( 'Repeater Field', 'gloo_for_elementor' ),
					'type'      => \Elementor\Controls_Manager::RAW_HTML,
					'raw'       => '<b>' . $repeater_field . '</b>',
					'condition' => [
						'follow_mode' => 'yes',
					],
				)
			);
		}

		$this->add_control(
			'field',
			array(
				'label'      => __( 'Repeater Field', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::TEXT,
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
			'collective_mode',
			array(
				'label'        => __( 'Collective Output', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'description'  => 'Returns a collection of subfield values from all repeater items.',
				'condition'    => [
					'follow_mode!' => 'yes'
				]
			)
		);

		$this->add_control(
			'collective_format',
			array(
				'label'     => __( 'Output Format', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'delimiter' => 'Delimiter-separated values',
					'array'     => 'Array',
				],
				'condition' => [
					'collective_mode' => 'yes'
				]
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label'       => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => ',',
				'condition'   => [
					'collective_mode'   => 'yes',
					'collective_format' => 'delimiter'
				],
			)
		);

		$this->add_control(
			'index',
			array(
				'label'       => __( 'Array Index', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => '0',
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'collective_mode!' => 'yes',
					'follow_mode!'     => 'yes'
				],
			)
		);

		$this->add_control(
			'follow_mode',
			[
				'label'        => __( 'Follow Listing Mode', 'gloo_for_elementor' ),
				'type'         => $is_listing_source_repeater ? \Elementor\Controls_Manager::SWITCHER : \Elementor\Controls_Manager::HIDDEN,
				'label_on'     => __( 'Yes', 'gloo' ),
				'label_off'    => __( 'No', 'gloo' ),
				'return_value' => 'yes',
				'condition'    => [
					'collective_mode!' => 'yes',
				],
			]
		);

$this->add_control(
			'collective_mode_follow',
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
			'delimiter_follow',
			array(
				'label'       => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => ',',
				'condition'   => [
					'collective_mode_follow'   => 'yes',
					'follow_mode' => 'yes'
				],
			)
		);
		
		$this->add_control(
			'index_follow',
			array(
				'label'       => __( 'Array Index', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => '0',
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'collective_mode_follow!' => 'yes',
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
	 * @return void
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_value( array $options = array() ) {

		$source          = $this->get_settings_for_display( 'source' );
		$context         = $this->get_settings_for_display( 'context' );
		$field           = $this->get_settings_for_display( 'field' );
		$subfield        = $this->get_settings_for_display( 'subfield' );
		$is_follow_mode  = $this->get_settings_for_display( 'follow_mode' ) === 'yes';
		$index           = intval( $this->get_settings_for_display( 'index' ) );
		$collective_args = [
			'collective_mode'   => $this->get_settings_for_display( 'collective_mode' ) === 'yes',
			'collective_format' => $this->get_settings_for_display( 'collective_format' ),
			'delimiter'         => $this->get_settings_for_display( 'delimiter' ),
		];

		if ( $source === 'jet_engine' && $context === 'options_page' ) {
			$field = $this->get_settings_for_display( 'jet_options_page_field' );
		}

		if ( $is_follow_mode ) {
			$index = jet_engine()->listings->data->get_index();

			// repeater already selected for the listing
			$is_listing_source_repeater = $this->is_listing_source_repeater();

			if ( $is_listing_source_repeater ) {
				$repeater_data = $this->get_listing_repeater();

				$field = $repeater_data['repeater_field'];
				if ( $repeater_data['repeater_source'] === 'jet_engine_options' ) {
					$repeater_data['repeater_source'] = 'jet_engine';
					$context                          = 'options_page';
					$field                            = $repeater_data['repeater_option'];
				}
				$source = $repeater_data['repeater_source'];
			}
		}

		switch ( $source ) {
			case 'acf':
				$result = $this->acf_get_data( $field, $subfield, $index, $context, $collective_args );
				break;
			case 'jet_engine':
				$result = $this->jet_engine_get_data( $field, $subfield, $index, $context, $collective_args );
				break;
			default:
				$result = apply_filters( 'gloo/modules/repeater_dynamic_tag/output', '', $this->get_settings_for_display() );
		}

		
			
			if((is_array($result) && count($result) >= 1)){
				$collective_mode_follow = $this->get_settings_for_display( 'collective_mode_follow' );
				if($collective_mode_follow == 'yes'){
					
					$delimiter_follow = $this->get_settings_for_display( 'delimiter_follow' );
					if(empty($delimiter_follow))
						$delimiter_follow = ', ';
					return implode($delimiter_follow, $result);
				}else{
					$index_follow           = intval( $this->get_settings_for_display( 'index_follow' ) );			 
				if($index_follow && isset($result[$index_follow]))
					  return $result[$index_follow];
					elseif(isset($result[0]))
					  return $result[0];
					else
					  return reset($result);
				}
				
				
				/*if(!empty($collective_args) && isset($collective_args['collective_mode']) && $collective_args['collective_mode']){
					if(isset($collective_args['collective_format']))
					{
						
					}
				}*/
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


	public function jet_engine_get_data( $field, $subfield, $index, $context, $collective_args = [] ) {
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

		if ( isset( $collective_args['collective_mode'] ) && $collective_args['collective_mode'] ) {
			$result = wp_list_pluck( $items, $subfield );
			if ( $collective_args['collective_format'] === 'delimiter' ) {
				$delimiter = $collective_args['delimiter'] ? $collective_args['delimiter'] : ',';
				$result    = implode( $delimiter, $result );
			}

			return $result;
		}

		$index = "item-" . $index;
		if ( ! isset( $items[ $index ] ) ) {
			return;
		}

		return isset( $items[ $index ][ $subfield ] ) ? $items[ $index ][ $subfield ] : null;
	}

	public function acf_get_data( $field, $subfield, $index, $context, $collective_args = [] ) {
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

		if ( isset( $collective_args['collective_mode'] ) && $collective_args['collective_mode'] ) {
			$result = wp_list_pluck( $items, $subfield );
			if ( $collective_args['collective_format'] === 'delimiter' ) {
				$delimiter = $collective_args['delimiter'] ? $collective_args['delimiter'] : ',';
				$result    = implode( $delimiter, $result );
			}

			return $result;
		}

		if ( ! isset( $items[ $index ] ) ) {
			return;
		}

		return isset( $items[ $index ][ $subfield ] ) ? $items[ $index ][ $subfield ] : null;
	}
}