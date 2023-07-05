<?php
namespace Gloo\Modules\Multi_Currency_Tag;

Class Currency_Tag extends \Elementor\Core\DynamicTags\Tag {


	private $prefix = 'gloo_multi_currency_';
	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'multi-currency-dynamic-tag';
	}

	/**
	 * Get Title
	 *
	 * Returns the title of the Tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Multicurrency Dynamic Tag', 'gloo_for_elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 
			\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY
		];
	}

	protected function _register_controls() {
 
		$labels = [];
		$data = \WOOMULTI_CURRENCY_Data::get_ins();
		$currencies = $data->get_list_currencies();
		$default_currency = $data->get_default_currency();

		if(!empty($currencies)) {
			foreach ($currencies as $key => $currency) {
				if( $currency != $default_currency) {
					$labels[$key] = $key;
				}
			}
		}

		$this->add_control(
			$this->prefix.'fixed_price',
			[
				'label' => esc_html__( 'Fixed Price ?', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			$this->prefix.'current_currency',
			[
				'label' => esc_html__( 'Show Current Currency', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off' => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			$this->prefix.'currencies',
			array(
				'label'   => __( 'Currencies', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'options' => $labels,
				'condition' => [
					$this->prefix.'current_currency!' => 'yes',
				],
			)
		);
		
	}

	public function render() {
		
		$fixed_price = $this->get_settings( $this->prefix.'fixed_price' );
		$current_curreny = $this->get_settings( $this->prefix.'current_currency' );
		$currency_code = $this->get_settings( $this->prefix.'currencies' );

		if($current_curreny == 'yes') {
			$data = \WOOMULTI_CURRENCY_Data::get_ins();
			$currency_code = $data->get_current_currency();
		}
		
		global $product;

		if ( is_a($product, 'WC_Product') ) {

			$product = wc_get_product( get_the_id() );		

			if ( $product->is_type( 'simple' ) ) {

				if( $fixed_price == 'yes') {
					$product_price = json_decode( get_post_meta( get_the_id(), '_regular_price_wmcp', true ), true );
							
					if(isset($product_price[$currency_code]) && !empty($product_price[$currency_code])) {
						echo wc_price( $product_price[$currency_code], [
							'currency' => $currency_code,
						] );
					}
				} else {
					$price = get_post_meta( get_the_id(), '_regular_price', true);
					echo wc_price(wmc_get_price( $price, $currency_code ));
				}
			}
		}
	}
}