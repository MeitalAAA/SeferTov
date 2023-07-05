<?php
namespace Gloo\Modules\Global_Elementor_Tags;

use Elementor\Plugin;

Class Global_Colors extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-global-colors';
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
		return __( 'Global Colors', 'gloo_for_elementor' );
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

		$result = [];

		$items = $this->get_color_data();

		if(!empty($items)) {
			foreach ( $items as $index => $item ) {
				$result[ $index ] = $item['title'];
			}
		}

		$this->add_control(
			'color_value',
			array(
				'label'   => __( 'Colors', 'gloo_for_elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => 'title',
				'options' => $result,
			)
		);

		$this->add_control(
			'remove_hash',
			[
				'label' => __( 'Remove #', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo_for_elementor' ),
				'label_off' => __( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

	}

	public function get_color_data($color_id = null) {
		
		$result = [];
		$kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();

		$system_items = $kit->get_settings_for_display( 'system_colors' );
		$custom_items = $kit->get_settings_for_display( 'custom_colors' );

		if ( ! $system_items ) {
			$system_items = [];
		}

		if ( ! $custom_items ) {
			$custom_items = [];
		}

		$items = array_merge( $system_items, $custom_items );

		foreach ( $items as $index => $item ) {
			$id = $item['_id'];
			$result[ $id ] = [
				'id' => $id,
				'title' => $item['title'],
				'value' => $item['color'],
			];
		}

		if(!empty($color_id)) {
			return $result[$color_id];
		} else {
			return $result;
		}
	}


	public function render() {
		
	 	$color_value = $this->get_settings( 'color_value' );
		$remove_hash = $this->get_settings( 'remove_hash' );

		if(!empty($color_value)){
			$colors = $this->get_color_data($color_value);
			$color =  $colors['value'];

			if($remove_hash == 'yes') {
				$color = str_replace('#','',$color);
			} 

			echo $color;
		}

 	}

}