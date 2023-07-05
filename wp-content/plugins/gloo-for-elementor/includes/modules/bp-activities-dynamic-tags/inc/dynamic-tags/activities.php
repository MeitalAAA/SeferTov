<?php
namespace Gloo\Modules\Activities_Dynamic_Tags;

Class Activities_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'bp-activities-tag';
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
		return __( 'BP Activities', 'gloo_for_elementor' );
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
 
        $this->add_control(
			'gloo_bp_activities_note',
			[
                'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Note: it will return BP activities', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
        );

	}

	public function render() {
	
		if ( ! function_exists( 'bp_get_user_meta' ) ) {
			return PHP_INT_MAX;
		}
		$user_id                   = get_current_user_id();
		$favorite_activity_entries = bp_get_user_meta( $user_id, 'bp_favorite_activities', true );
		if ( ! $favorite_activity_entries ) {
			return PHP_INT_MAX;
		}

		echo implode( ",", $favorite_activity_entries );
	}

}