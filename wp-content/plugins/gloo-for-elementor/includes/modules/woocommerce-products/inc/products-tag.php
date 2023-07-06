<?php

namespace Gloo\Modules\Woocommerce_Products_Tags;

class Products_Tags extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-woocommerce-products-tags';
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
		return __( 'Woooo Related Products', 'gloo_for_elementor' );
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

		$output_option = [
			'type_ul'        => 'Ul Structure',
			'type_ol'        => 'Ol Structure',
			'type_delimiter' => 'Delimeter',
			'type_lenght'    => 'Array Length',
			'type_array'     => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		$return_value = [
			'title' => 'Title',
			'slug'  => 'Slug',
			'id'    => 'ID',
			'link'  => 'Link'
		];

		$product_types = [
			'related_product' => 'Related Products',
			'upsell_product'  => 'Upsells Products',
			'cross_product'   => 'Cross Sells Products',
			'best_seller'     => 'Best Seller Products',
		];

		$this->add_control(
			'product_type',
			array(
				'label'   => __( 'Product Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'related_product',
				'options' => $product_types,
			)
		);

		$this->add_control(
			'enable_timeframe',
			[
				'label'        => __( 'Enable Timeframe', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'product_type' => 'best_seller',
				],
			]
		);

		$this->add_control(
			'timeframe',
			array(
				'label'       => __( 'Timeframe', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'description' => __( 'Get best seller product from last days/months' ),
				'condition'   => [
					'product_type'     => 'best_seller',
					'enable_timeframe' => 'yes'
				],
				'options'     => [
					'days'  => 'Days',
					'month' => 'Months'
				],
				'default'     => 'days'
			)
		);

		$this->add_control(
			'timeframe_value',
			array(
				'label'     => __( 'Days/Month', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'condition' => [
					'product_type'     => 'best_seller',
					'enable_timeframe' => 'yes'
				],
				'default'   => '',
			)
		);

		$this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_delimiter',
				'options' => $output_option,
			)
		);

		$this->add_control(
			'one_per_line_type',
			array(
				'label'     => __( 'Line Break Type', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default' => 'php',
				'options' => array('php' => 'PhP', 'html' => 'HTML'),
				'condition' => [
					'field_output' => 'one_per_line'
				],
			)
		);

		$this->add_control(
			'return_value',
			array(
				'label'     => __( 'Return Value', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'condition' => [
					'field_output' => array( 'type_ul', 'type_ol', 'type_delimiter', 'type_array' )
				],
				'default'   => 'id',
				'options'   => $return_value,
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'field_output' => 'type_delimiter'
				],
				'default'   => ',',
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'condition' => [
					'field_output' => 'type_array'
				],
			)
		);

		$this->add_control(
			'limit_results',
			array(
				'label'       => __( 'Limit Results', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => 'all',
			)
		);

		$this->add_control(
			'enable_zero',
			[
				'label'        => __( 'Enable Zero', 'gloo_for_elementor' ),
				'description'  => __( 'if empty return 0', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'current_category',
			[
				'label'        => __( 'Relative to current category', 'gloo_for_elementor' ),
				'description'  => __( 'only return results of current category', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
			]
		);

	}

	public function render() {
		$product_type     = $this->get_settings( 'product_type' );
		$return_value     = $this->get_settings( 'return_value' );
		$field_output     = $this->get_settings( 'field_output' );
		$delimiter        = $this->get_settings( 'delimiter' );
		$array_index      = $this->get_settings( 'array_index' );
		$enable_zero      = $this->get_settings( 'enable_zero' );
		$current_category = $this->get_settings( 'current_category' );
		$limit_results    = intval( $this->get_settings( 'limit_results' ) );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		$output = '';

		$product_data = array();

		global $product;

		if ( $product_type == 'related_product' ) {

			if ( is_a( $product, 'WC_Product' ) ) {

				$product = wc_get_product( get_the_id() );

				$args = array(
					'posts_per_page' => 999,
				);

				$related_products = wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() );

				if ( ! empty( $related_products ) ) {
					foreach ( $related_products as $related_product ) {
						$product_data[] = get_post( $related_product );
					}
				}
			}
		} elseif ( $product_type == 'cross_product' ) {
			/* upsell product ids */
			if ( is_a( $product, 'WC_Product' ) ) {
				$product = wc_get_product( get_the_id() );

				$cross_product = $product->get_cross_sell_ids();

				if ( ! empty( $cross_product ) ) {
					foreach ( $cross_product as $crosssell_item ) {
						$product_data[] = get_post( $crosssell_item );
					}
				}
			}
		} elseif ( $product_type == 'upsell_product' ) {
			/* upsell product ids */
			if ( is_a( $product, 'WC_Product' ) ) {
				$product = wc_get_product( get_the_id() );

				$upsell_product = $product->get_upsell_ids();

				if ( ! empty( $upsell_product ) ) {
					foreach ( $upsell_product as $upsell_item ) {
						$product_data[] = get_post( $upsell_item );
					}
				}
			}
		} elseif ( $product_type == 'best_seller' ) {
			/* best seller product ids */
			$enable_timeframe = $this->get_settings( 'enable_timeframe' );
			$timeframe        = $this->get_settings( 'timeframe' );
			$timeframe_value  = $this->get_settings( 'timeframe_value' );

			if ( $enable_timeframe == 'yes' ) {

				if ( $timeframe == 'month' ) {
					$date = date( 'Y-m-d', strtotime( '-' . $timeframe_value . ' month' ) );
				} else {
					$date = date( 'Y-m-d', strtotime( '-' . $timeframe_value . ' days' ) );
				}
			} else {
				$date = '1970-01-10';
			}

			global $wpdb;

			$sql = "
			SELECT p.* , COUNT(oim2.meta_value) as count
			FROM {$wpdb->prefix}posts p
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim
				ON p.ID = oim.meta_value
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim2
				ON oim.order_item_id = oim2.order_item_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
				ON oim.order_item_id = oi.order_item_id
			INNER JOIN {$wpdb->prefix}posts as o
				ON o.ID = oi.order_id
			WHERE p.post_type = 'product'
			AND p.post_status = 'publish'
			AND o.post_status IN ('wc-processing','wc-completed')
			AND o.post_date >= '$date'
			AND oim.meta_key = '_product_id'
			AND oim2.meta_key = '_qty'
			GROUP BY p.ID
			ORDER BY COUNT(oim2.meta_value) + 0 DESC";


			$results = $wpdb->get_results( $sql );

			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					$product_data[] = $result;
				}
			}


		}

		if ( $current_category ) { // results relative to current category
			$current_category_query = new \WP_Query( array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => array( get_queried_object_id() ),
					)
				)
			) );


			$new_items = [];
			while ( $current_category_query->have_posts() ) : $current_category_query->the_post();
				$key = array_search( get_the_ID(), array_column( $product_data, 'ID' ) );
				if ( $key !== false && isset( $product_data[ $key ] ) ) {
					$new_items[ $key ] = $product_data[ $key ];
				}
			endwhile;
			$product_data = $new_items;
			wp_reset_postdata();
		}

		if ( ! empty( $product_data ) ) {

			if ( $return_value == 'slug' ) {

				foreach ( $product_data as $product_item ) {
					$product_detail[] = urldecode( $product_item->post_name );
				}

			} elseif ( $return_value == 'id' ) {
				foreach ( $product_data as $product_item ) {
					$product_detail[] = $product_item->ID;
				}
			} elseif ( $return_value == 'link' ) {

				foreach ( $product_data as $product_item ) {
					$product_detail[] = urldecode( get_permalink( $product_item->ID ) );
				}

			} else {
				foreach ( $product_data as $product_item ) {
					$product_detail[] = $product_item->post_title;
				}
			}
		} else {

			if ( $enable_zero == 'yes' ) {
				echo '0';
			} else {
				return;
			}

		}


		if ( ! empty( $product_detail ) && is_array( $product_detail ) ) {

			if ( $limit_results ) { // limit results
				$product_detail = array_slice( $product_detail, 0, $limit_results );
			}

			if ( $field_output == 'type_ul' ) {

				$output .= '<ul class="attribute-ul">';

				foreach ( $product_detail as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ul>';

			} else if ( $field_output == 'type_ol' ) {

				$output .= '<ol class="attribute-ol">';

				foreach ( $product_detail as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ol>';

			} else if ( $field_output == 'type_lenght' ) {

				$output = count( $product_detail );

			} else if ( $field_output == 'type_delimiter' && ! empty( $delimiter ) ) {

				$output = implode( $delimiter, $product_detail );

			} else if ( $field_output == 'type_array' && ! empty( $array_index ) ) {

				if ( isset( $product_detail[ $array_index ] ) && ! empty( $product_detail[ $array_index ] ) ) {
					$output = $product_detail[ $array_index ];
				}

			}
			else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $product_detail );
				else
					$output = implode( PHP_EOL, $product_detail );

			}

			echo $output;

		}

	}

}