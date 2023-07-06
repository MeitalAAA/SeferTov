<?php
namespace Gloo\Modules\Global_Elementor_Tags;

use Elementor\Plugin;

Class Global_Fonts extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-global-fonts';
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
		return __( 'Global Fonts', 'gloo_for_elementor' );
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

		$options = [];

		$title_only = true;

		$options = $this->get_typography_data($title_only);

		$return = array(
			'font_family' => 'Font family',
			'font_size' => 'Size',
			'font_weight' => 'Weight',
			'line_height' => 'Line height',
			'letter_spacing' => 'Letter spacing'
		);

		$this->add_control(
			'gloo_global_style',
			array(
				'label'   => __( 'Style', 'gloo_for_elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => '',
				'options' => $options,
			)
		);

		$this->add_control(
			'gloo_responsive',
			array(
				'label' => __( 'Responsive', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo_for_elementor' ),
				'label_off' => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
			)
		);

		$responsive = array(
			'tablet' => 'Tablet',
			'mobile' => 'Mobile'
		);

		$this->add_control(
			'gloo_responsive_option',
			array(
				'label'   => __( 'Layout', 'gloo_for_elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => '',
				'options' => $responsive,
				'condition' => [
					'gloo_responsive' => 'yes'
				],
			)
		);

		$this->add_control(
			'gloo_return_value',
			array(
				'label'   => __( 'Return Value', 'gloo_for_elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => 'font_family',
				'options' => $return,
			)
		);
	
		$this->add_control(
			'gloo_fallback_family',
			array(
				'label'   => __( 'Fallback Family', 'gloo_for_elementor' ),
                'type'    => \Elementor\Controls_Manager::FONT,
				'default' => '',
				'condition' => [
					'gloo_return_value' => 'font_family'
				],
			)
		);

	}

	public function get_typography_data($title = false) {
		
		$result = [];

		$kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();

		// Use raw settings that doesn't have default values.
		$kit_raw_settings = $kit->get_data( 'settings' );

		if ( isset( $kit_raw_settings['system_typography'] ) ) {
			$system_items = $kit_raw_settings['system_typography'];
		} else {
			// Get default items, but without empty defaults.
			$control = $kit->get_controls( 'system_typography' );
			$system_items = $control['default'];
		}

		$custom_items = $kit->get_settings( 'custom_typography' );

		if ( ! $custom_items ) {
			$custom_items = [];
		}

		$items = array_merge( $system_items, $custom_items );

		if($title) {
			
			foreach ( $items as $index => &$item ) {
				$id = $item['_id'];
				$result[$id] = $item['title'];
			}

			return $result;

		} else {
			return $items;
		}
	}

	public function render() {
		
	 	$global_style = $this->get_settings( 'gloo_global_style' );
		$responsive = $this->get_settings( 'gloo_responsive' );
		$responsive_option = $this->get_settings( 'gloo_responsive_option' );
		$return_value = $this->get_settings( 'gloo_return_value' );
		$fallback_family = $this->get_settings( 'gloo_fallback_family' );
		
		$data = $this->get_typography_data();
		$data_result = array();

		$output = '';

		if(!empty($data)) {
			
			$key_prefix = 'typography_';

			foreach($data as $item) {
				$data_result[$item['_id']] = $item;
			}

			if(!empty($global_style)) {

				$result = $data_result[$global_style]; 
				
				if(!empty($result)) {
				
					if(!empty($return_value)) {
						
						if($responsive_option == 'mobile') {

							$output = $result[$key_prefix.$return_value.'_mobile'];

						} elseif($responsive_option == 'tablet') {
							
							$output = $result[$key_prefix.$return_value.'_tablet'];

						} else {
							
							$output = $result[$key_prefix.$return_value];

						}
						
						/** handle array return */
						if($return_value == 'font_size' || $return_value == 'line_height'|| $return_value == 'letter_spacing') {
					
							echo $output['size'].$output['unit'];
						} else {

							if($return_value == 'font_family') {

								if(!empty($fallback_family)) {
									$output = $output.','.$fallback_family;
								} else {
									$output = $output;
								}
							}

							echo $output;
						}

					} 
				}
			}
		}
 	}

}