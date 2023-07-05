<?php

namespace Gloo\Modules\Dynamify_Repeaters;

use ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use ElementorPro\Modules\QueryControl\Module as Query_Module;


class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	private static $post_ids = [];

	public $allowed_tags = [
		'gloo-dynamify-tag',
		'gloo-dynamify-tag-image',
		'gloo-dynamify-tag-gallery',
		'gloo-dynamify-tag-wc-variations',
	];

	public $element_types = [
		'section',
		'column',
		'widget',
	];
	public $slug = 'dynamify_repeaters';

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
		add_action( "elementor/element/after_section_end", array( $this, 'register_controls' ), 10, 2 );

		foreach ( $this->element_types as $el ) {
			add_action( 'elementor/frontend/' . $el . '/before_render', array( $this, 'dynamify_repeater' ), 1, 1 );
		}

		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_dynamic_tags' ) );

		add_filter( "elementor/query/get_autocomplete/display/post_type_prefix", array(
			$this,
			'elementor_query_post_type_prefix'
		), 20, 3 );

	}

	public function elementor_query_post_type_prefix( $post_title, $post_id, $data ) {

		$post_type = get_post_type_object( get_post_type( $post_id ) );

		if ( ! $post_type || ! $post_type->labels->singular_name ) {
			return $post_title;
		}

		return "{$post_type->labels->singular_name}: {$post_title}";
	}

	public function register_dynamic_tags( $dynamic_tags ) {

		// Include the Dynamic tag class file
		include_once( gloo()->modules_path( 'dynamify-repeaters/inc/dynamify-tag.php' ) );
		include_once( gloo()->modules_path( 'dynamify-repeaters/inc/dynamify-tag-image.php' ) );
		include_once( gloo()->modules_path( 'dynamify-repeaters/inc/dynamify-tag-gallery.php' ) );

		// Register the tag
		$dynamic_tags->register_tag( 'Gloo\Modules\Dynamify_Repeaters\Dynamify_Tag' );
		$dynamic_tags->register_tag( 'Gloo\Modules\Dynamify_Repeaters\Dynamify_Tag_Image' );
		$dynamic_tags->register_tag( 'Gloo\Modules\Dynamify_Repeaters\Dynamify_Tag_Gallery' );

		// WooCommerce tag
		if ( class_exists( 'WooCommerce' ) ) {
			include_once( gloo()->modules_path( 'dynamify-repeaters/inc/dynamify-tag-wc-variations.php' ) );
			$dynamic_tags->register_tag( 'Gloo\Modules\Dynamify_Repeaters\Dynamify_Tag_WC_Variations' );
		}

	}


	public function register_controls( $element, $section ) {

		if ( empty( $element ) ) {
			return;
		}

		if ( ! in_array( $element->get_type(), $this->element_types ) ) {
			return;
		}

		$all_controls = $element->get_controls();

		foreach ( $all_controls as $setting => $control ) {

			if ( \Elementor\Controls_Manager::REPEATER !== $control['type'] || ! $control['type'] ) {
				continue;
			}
			$has_control = $element->get_controls( 'dynamify_' . $setting . '_repeater' );

			if ( $has_control || $setting == 'gca1_manual_products' || $setting == 'gca1_new_repeater_products' || (method_exists($element, 'get_group_name') && $element->get_group_name() == 'forms')) {
				continue;
			}
			
			$element->start_injection( [
				'of' => $setting,
				'at' => 'before'
			] );

			$label = isset( $control['label'] ) && $control['label'] ? ': ' . $control['label'] : '';

			$control_parameters = [
				'label'   => 'Dynamify ' . $label,
				'type'    => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'classes' => 'gloo-dynamify-main-control',

			];

			if ( isset( $control['condition'] ) ) {
				$control_parameters['condition'] = $control['condition'];
			} elseif ( isset( $control['conditions'] ) ) {
				$control_parameters['conditions'] = $control['conditions'];
			}

			$element->add_control(
				'gloo_dynamify_' . $setting,
				$control_parameters
			);

			$element->start_popover();

			$control_args['condition'] = [ 'gloo_dynamify_' . $setting => 'yes' ];
			$element->add_control(
				'dynamify_' . $setting . '_repeater',
				[
					'label'        => 'Dynamify Repeater',
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
					'label_off'    => __( 'No', 'gloo_for_elementor' ),
					'return_value' => 'yes',
					'condition'    => [
						'gloo_dynamify_' . $setting => 'yes'
					]
				]
			);

			$element->add_control(
				'dynamify_' . $setting . '_max',
				[
					'label'       => 'Max Items',
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'placeholder' => "auto",
					'condition'   => [
						'gloo_dynamify_' . $setting => 'yes'
					]
				]
			);

			$sources = [
				'post_types' => array(
					'label'   => __( 'Post Type', 'gloo_for_elementor' ),
					'options' => [
						'post_type' => 'Post Type'
					]
				),
			];

			$repeater_field_sources = [];

			if ( function_exists( 'jet_engine' ) ) {
				$repeater_field_sources['jet_engine'] = __( 'JetEngine', 'gloo_for_elementor' );
			}

			if ( function_exists( 'acf' ) ) {
				$repeater_field_sources['acf'] = __( 'ACF', 'gloo_for_elementor' );
			}

			if ( $repeater_field_sources ) {
				$sources['repeater_field'] = [
					'label'   => __( 'Repeater Field', 'gloo_for_elementor' ),
					'options' => $repeater_field_sources,
				];
			}

			// requires WooCommerce
			if ( class_exists( 'WooCommerce' ) ) {
				$sources['woocommerce'] = [
					'label'   => __( 'WooCommerce', 'gloo_for_elementor' ),
					'options' => [ 'woocommerce_variations' => 'WooCommerce Variations' ],
				];
			}

			$element->add_control(
				'dynamify_' . $setting . '_source',
				array(
					'type'        => \Elementor\Controls_Manager::SELECT,
					'label'       => __( 'Repeater Source', 'gloo_for_elementor' ),
					'label_block' => true,
					'groups'      => $sources,
				)
			);

			// requires WooCommerce
			if ( class_exists( 'WooCommerce' ) ) {
				$element->add_control(
					'dynamify_' . $setting . '_wc_product',
					array(
						'label'          => __( 'Product', 'gloo_for_elementor' ),
						'label_block'    => true,
						// 'type'           => Query_Module::QUERY_CONTROL_ID,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'autocomplete'   => [
							'object' => Query_Module::QUERY_OBJECT_POST,
							'query'  => [
								'post_type' => 'product',
								'tax_query' => array(
									array(
										'taxonomy' => 'product_type',
										'field'    => 'slug',
										'terms'    => 'variable',
									),
								),
							],
						],
						'select2options' => [
							'placeholder' => 'Default: Current Product',
						],
						'description'    => 'Defaults to current product if it is within the single product page context.',
						'condition'      => [
							'dynamify_' . $setting . '_source' => [ 'woocommerce_variations' ],
						]
					)
				);
			}
			$element->add_group_control(
				Group_Control_Query::get_type(),
				[
					'name'      => 'dynamify_' . $setting . '_query',
					'exclude'   => [
						'posts_per_page',
						'query_id'
					],
					'condition' => [
						'dynamify_' . $setting . '_source' => 'post_type'
					]
				]
			);


			$element->add_control(
				'dynamify_' . $setting . '_context',
				array(
					'type'        => \Elementor\Controls_Manager::SELECT,
					'label'       => __( 'Context', 'gloo_for_elementor' ),
					'label_block' => true,
					'default'     => 'post',
					'options'     => array(
						'post'         => __( 'Post', 'gloo_for_elementor' ),
						'user'         => __( 'User', 'gloo_for_elementor' ),
						'term'         => __( 'Term', 'gloo_for_elementor' ),
						'options_page' => __( 'Options Page', 'gloo_for_elementor' ),
					),
					'condition'   => [
						'dynamify_' . $setting . '_source' => [ 'jet_engine', 'acf' ],
					]
				)
			);


			$element->add_control(
				'dynamify_' . $setting . '_post',
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
						'dynamify_' . $setting . '_source'  => [ 'jet_engine', 'acf' ],
						'dynamify_' . $setting . '_context' => 'post',
					]
				)
			);

			$element->add_control(
				'dynamify_' . $setting . '_term',
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
					'description'    => __( 'Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'gloo_for_elementor' ),
					'condition'      => [
						'dynamify_' . $setting . '_source'  => [ 'jet_engine', 'acf' ],
						'dynamify_' . $setting . '_context' => 'term',
					]
				)
			);

			$element->add_control(
				'dynamify_' . $setting . '_user',
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
						'dynamify_' . $setting . '_source'  => [ 'jet_engine', 'acf' ],
						'dynamify_' . $setting . '_context' => 'user',
					]
				)
			);

			if ( function_exists( 'jet_engine' ) ) {

				$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'repeater' );

				if ( ! empty( $options_pages_select ) ) {
					$element->add_control(
						'dynamify_' . $setting . '_jet_options_page_field',
						array(
							'label'     => __( 'Repeater Field', 'gloo_for_elementor' ),
							'type'      => \Elementor\Controls_Manager::SELECT,
							'default'   => '',
							'groups'    => $options_pages_select,
							'condition' => [
								'dynamify_' . $setting . '_context' => 'options_page',
								'dynamify_' . $setting . '_source'  => 'jet_engine',
							],
						)
					);
				}
			}


			$element->add_control(
				'dynamify_' . $setting . '_field',
				array(
					'label'      => __( 'Repeater Field', 'gloo_for_elementor' ),
					'type'       => \Elementor\Controls_Manager::TEXT,
					'dynamic'    => [
						'active' => true,
					],
					'conditions' => [
						'relation' => 'and',
						'terms'    => [
							[
								'relation' => 'or',
								'terms'    => [
									[
										'name'     => 'dynamify_' . $setting . '_source',
										'operator' => '==',
										'value'    => 'acf'
									],
									[
										'name'     => 'dynamify_' . $setting . '_source',
										'operator' => '==',
										'value'    => 'jet_engine'
									]
								]
							],
							[
								'relation' => 'or',
								'terms'    => [
									[
										'name'     => 'dynamify_' . $setting . '_context',
										'operator' => '!==',
										'value'    => 'options_page'
									],
									[
										'name'     => 'dynamify_' . $setting . '_source',
										'operator' => '!==',
										'value'    => 'jet_engine'
									],
								],
							],
						],
					]
				)
			);
			$element->end_popover();
			$element->end_injection();
		}
	}

	public function dynamify_repeater( $element ) {
		$settings = $element->get_settings();


		$dynamify = [];
		global $post;


		foreach ( $settings as $key => $value ) {
			if ( strpos( $key, 'dynamify_' ) === 0 && substr_compare( $key, '_repeater', strlen( $key ) - strlen( '_repeater' ), strlen( '_repeater' ) ) === 0 && $value ) {
				$dynamify[] = $key;
			}
		}

		if ( ! $dynamify ) {
			return;
		}


		foreach ( $dynamify as $repeater_item ) {
			$key = substr( $repeater_item, 0, - 9 ); // remove _repeater at the end
			$key = substr( $key, 9 ); // remove dynamify_ at the start
			if ( ! $key ) {
				continue;
			}

			$max                    = $element->get_settings( "dynamify_{$key}_max" );
			$source                 = $element->get_settings( "dynamify_{$key}_source" );
			$context                = $element->get_settings( "dynamify_{$key}_context" );
			$context_post           = $element->get_settings( "dynamify_{$key}_post" );
			$context_term           = $element->get_settings( "dynamify_{$key}_term" );
			$context_user           = $element->get_settings( "dynamify_{$key}_user" );
			$field                  = $element->get_settings( "dynamify_{$key}_field" );
			$jet_options_page_field = $element->get_settings( "dynamify_{$key}_jet_options_page_field" );

			if ( $source === 'jet_engine' && $context === 'options_page' ) {
				$field = $element->get_settings( "dynamify_{$key}_jet_options_page_field" );
			}

			if ( ! $source ) {
				continue; // no source
			}

			switch ( $source ) {
				case 'acf':
					$count = $this->acf_get_count( $field, $context, $element, $key );
					break;
				case 'jet_engine':
					$count = $this->jet_engine_get_count( $field, $context, $element, $key );
					break;
				case 'woocommerce_variations':
					$field = $element->get_settings( "dynamify_{$key}_wc_product" );
					$count = $this->wc_variations_get_count( $field );
					break;
				default :

					$query_args = [
						'posts_per_page' => - 1,
						'fields'         => 'ids',
					];

					$elementor_query = Query_Module::instance();
					$query_result    = $elementor_query->get_query( $element, "dynamify_{$key}_query", $query_args, [] );

					$count = count( $query_result->posts );

					if ( ! self::$post_ids || ! isset( self::$post_ids[ $element->get_id() ] ) ) {
						self::$post_ids[ $element->get_id() ] = $query_result->posts;
					}
			}

			$repeater = $element->get_settings( $key );

			if ( ! $repeater || ! is_array( $repeater ) || ! isset( $repeater[0] ) ) {
				continue;
			}

			// empty if there is no value
			if ( ! isset( $count ) || ! $count ) {
				$element->set_settings( $key, [] );
				continue;
			}

			$count = $max && $max < $count ? $max : $count;

			$dynamic_data = null;
			if ( isset( $repeater[0]['__dynamic__'] ) && is_array( $repeater[0]['__dynamic__'] ) ) {
				foreach ( $repeater[0]['__dynamic__'] as $dynamic_item_key => $dynamic_item ) {

					preg_match( '/name="(.*?(?="))"/', $dynamic_item, $tag_name_match );
					preg_match( '/settings="(.*?(?="]))/', $dynamic_item, $tag_settings_match );

					if ( ! $tag_name_match || ! $tag_settings_match ) {
						continue;
					}

					if ( ! in_array( $tag_name_match[1], $this->allowed_tags ) ) {
						continue;
					}

					$tag_settings = json_decode( urldecode( $tag_settings_match[1] ), true );

					$dynamify_settings = [
						'source'                 => $source,
						'context'                => $context,
						'field'                  => $field,
						'jet_options_page_field' => $jet_options_page_field,
						'post'                   => $context_post,
						'term'                   => $context_term,
						'user'                   => $context_user,
						'index'                  => '{{{dynamify_index}}}',
					];

					$tag_settings = array_merge( $tag_settings, $dynamify_settings );
					$tag_settings = urlencode( wp_json_encode( $tag_settings, JSON_FORCE_OBJECT ) );

					$repeater[0]['__dynamic__'][ $dynamic_item_key ] = preg_replace( '/settings="(.*?(?="]))/', 'settings="' . $tag_settings, $dynamic_item );
				}
			}

			// is the repeater source a post type
			$is_for_post_type = $source === 'post_type' && self::$post_ids && isset( self::$post_ids[ $element->get_id() ] ) && self::$post_ids[ $element->get_id() ];

			if ( intval( $count ) ) {
				for ( $i = $is_for_post_type ? 0 : 1; $i <= intval( $count ); $i ++ ) {

					// check if the source is a post type
					if ( $is_for_post_type ) {
						$post_ids = self::$post_ids[ $element->get_id() ];
						// parse dynamic tags
						if ( $post_ids ) {
							$post_id_key = key( $post_ids );

							$post = get_post( $post_ids[ $post_id_key ], OBJECT );
							setup_postdata( $post );
							unset( self::$post_ids[ $element->get_id() ][ $post_id_key ] );
							$settings = $element->parse_dynamic_settings( $element->get_settings() );

							if ( isset( $settings[ $key ][0]['__dynamic__'] ) ) {
								unset( $settings[ $key ][0]['__dynamic__'] );
							}

							$settings[ $key ][0]['_id'] = uniqid();
							$repeater[ $i ]             = $settings[ $key ][0];
						}
					} else {

						$repeater[ $i ]        = $repeater[0];
						$repeater[ $i ]['_id'] = uniqid();

						if ( ! isset( $repeater[ $i ]['__dynamic__'] ) || ! $repeater[ $i ]['__dynamic__'] ) {
							continue;
						}

						foreach ( $repeater[ $i ]['__dynamic__'] as $dynamic_item_key => $dynamic_item ) {
							$repeater[ $i ]['__dynamic__'][ $dynamic_item_key ] = str_replace( urlencode( wp_json_encode( '{{{dynamify_index}}}' ) ), urlencode( wp_json_encode( $i - 1 ) ), $dynamic_item );
						}
					}
				}

				if ( ! $is_for_post_type ) {
					unset( $repeater[0] );
				}

			}

			wp_reset_query();
			$element->set_settings( $key, $repeater );
		}
	}


	public function jet_engine_get_count( $field, $context, $element, $key ) {
		if ( ! function_exists( 'jet_engine' ) ) {
			return;
		}

		switch ( $context ) {
			case 'options_page' :
				$items = jet_engine()->listings->data->get_option( $field );
				break;
			case 'term' :
				$term_id = $element->get_settings( "dynamify_{$key}_term" );
				$term_id = $term_id ? $term_id : get_queried_object_id();
				$items   = $items = get_term_meta( $term_id, $field, true );
				break;
			case 'user' :
				$user_id = $element->get_settings( "dynamify_{$key}_user" );
				$user_id = $user_id ? $user_id : get_current_user_id();
				$items   = $items = get_user_meta( $user_id, $field, true );
				break;
			default : // current_post
				$post_id = $element->get_settings( "dynamify_{$key}_post" );
				$post_id = $post_id ? $post_id : get_the_ID();
				$items   = get_post_meta( $post_id, $field, true );
		}

		if ( ! $items ) { // no items
			return;
		}


		return is_array( $items ) ? count( $items ) : 0;
	}

	public function acf_get_count( $field, $context, $element, $key ) {
		if ( ! function_exists( 'acf' ) ) {
			return;
		}

		switch ( $context ) {
			case 'options_page' :
				$items = get_field( $field, 'option' );
				break;
			case 'term' :
				$term_id = $element->get_settings( "dynamify_{$key}_term" ) ? $element->get_settings( "dynamify_{$key}_term" ) : get_queried_object_id();
				$term    = get_term( $term_id );
				$items   = get_field( $field, "{$term->taxonomy}_{$term_id}" );
				break;
			case 'user' :
				$user_id = $element->get_settings( "dynamify_{$key}_user" );
				$user_id = $user_id ? $user_id : get_queried_object_id();
				$items   = get_field( $field, "user_{$user_id}" );
				break;
			default : // current_post
				$post_id = $element->get_settings( "dynamify_{$key}_post" );
				$post_id = $post_id ? $post_id : get_the_ID();
				$items   = get_field( $field, $post_id );
		}


		if ( ! $items ) { // no items
			return;
		}

		return is_array( $items ) ? count( $items ) : 0;
	}

	public function wc_variations_get_count( $product_id = null ) {

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}
		$variations = $product->get_available_variations();

		return is_array( $variations ) ? count( $variations ) : 0;
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