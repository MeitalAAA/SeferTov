<?php

namespace Elementor;

use \Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

/**
 * Description of Gloo_Controls_Manager
 */
class Gloo_Controls_Manager extends Controls_Manager {

	/**
	 * Add control to stack.
	 *
	 * This method adds a new control to the stack.
	 *
	 * @param Controls_Stack $element Element stack.
	 * @param string $control_id Control ID.
	 * @param array $control_data Control data.
	 * @param array $options Optional. Control additional options.
	 *                                     Default is an empty array.
	 *
	 * @return bool True if control added, False otherwise.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function add_control_to_stack( Controls_Stack $element, $control_id, $control_data, $options = [] ) {
		if ( ! in_array( $element->get_name(), array( 'popup_triggers', 'popup_timing' ) ) ) {
			$control_data = $this->enable_dynamic_tags( $control_data );
		}

		return parent::add_control_to_stack( $element, $control_id, $control_data, $options );
	}

	public function enable_dynamic_tags( $control_data ) {

		if ( empty( $control_data ) ) {
			return $control_data; // bail early
		}

		// enable dynamic tags
		foreach ( $control_data as $key => $value ) {
			if ( 'dynamic' != $key && is_array( $value ) ) {
				$control_data[ $key ] = $this->enable_dynamic_tags( $value );
			}
		}


		
		if ( isset( $control_data['type'] ) && ! is_array( $control_data['type'] ) ) {

			if ( in_array( $control_data['type'], $this->get_dynamic_control_types() ) ) {
				$control_data['dynamic']['active'] = true;
			}
		}

		// 	$typography_group = Plugin::$instance->controls_manager->get_control_groups(Controls_Manager::TEXT_SHADOW);

		// 	// echo '<pre>';
		// 	// print_r($typography_group);	
		// 	// echo '</pre>';

		// 	if ( $control_data['type']  == Controls_Manager::TEXT_SHADOW ) {
		// 		$control_obj = Plugin::$instance->controls_manager->get_control( 'text_shadow_type' );
		// 		//$typography_group = Plugin::$instance->controls_manager->get_control_groups('text_shadow_type');

			 
		// 		// $typography_group = Plugin::$instance->controls_manager->get_control_groups( Controls_Manager::TEXT_SHADOW );

		// 		// $control_data['global'] = array(
		// 		// 	'default' => Global_Colors::COLOR_PRIMARY,
		// 		// );
 
		// 		echo '<pre>';
		// 		print_r($control_obj);	
		// 		echo '</pre>';
		// 	}
		// }

		// set all categories
		if ( isset( $control_data['dynamic']['categories'] ) ) {

			if ( $control_data['type'] === 'gallery' ) {
				$dynamic_tags = [
					DynamicTags::GALLERY_CATEGORY
				];
			}

			if ( $control_data['type'] === 'media' || $control_data['type'] === 'icons' ) {
				$dynamic_tags = [
					DynamicTags::MEDIA_CATEGORY,
					DynamicTags::IMAGE_CATEGORY,
				];
			}


			if ( ! isset( $dynamic_tags ) ) {
				$dynamic_tags = [
					DynamicTags::BASE_GROUP,
					DynamicTags::TEXT_CATEGORY,
					DynamicTags::URL_CATEGORY,
					DynamicTags::POST_META_CATEGORY,
					DynamicTags::NUMBER_CATEGORY,
					DynamicTags::COLOR_CATEGORY,
				];	


				$image_tags = [
					DynamicTags::GALLERY_CATEGORY,
					DynamicTags::MEDIA_CATEGORY,
					DynamicTags::IMAGE_CATEGORY,
				];

				foreach ( $image_tags as $image_tag ) {
					if ( ( $key = array_search( $image_tag, $control_data['dynamic']['categories'] ) ) !== false ) {
						unset( $control_data['dynamic']['categories'][ $key ] );
					}
				}

			}

			$control_data['dynamic']['categories'] = array_unique( array_merge( $control_data['dynamic']['categories'], $dynamic_tags ) );
		}
 
		return $control_data;
	}

	public function get_dynamic_control_types() { // hook to add new controls
		return apply_filters( 'gloo/fluid_dynamics/dynamic_tag_control_types', [
			Controls_Manager::TEXT,
			Controls_Manager::TEXTAREA,
			Controls_Manager::WYSIWYG,
			Controls_Manager::NUMBER,
			Controls_Manager::URL,
			Controls_Manager::COLOR,
			Controls_Manager::SLIDER,
			Controls_Manager::MEDIA,
			Controls_Manager::GALLERY,
		] );
	}

}