<?php
namespace Gloo\Modules\Community_Dynamic_Tags;

Class Newest_Memebers_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'bp-newest-memebers-tag';
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
		return __( 'BP Newest Memebers', 'gloo_for_elementor' );
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
			'gloo_bp_newest_memebers',
			[
                'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Note: it will return newest memebers', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
        );

	}

	public function render() {
	
		$args = [
			'per_page' => 0, // get all
			'type'     => 'newest',
			'exclude'  => get_current_user_id(),
		];

		$user_query = new \BP_User_Query( $args );
		if ( ! $user_query->results ) {
			// no results
			return PHP_INT_MAX;
		}
		$user_ids = $user_query->user_ids;
		if ( ! $user_ids ) {
			// no user ids
			return PHP_INT_MAX;
		}
		// double check
		$key = array_search( get_current_user_id(), $user_ids );
		if ( $key !== false ) {
			unset( $user_ids[ $key ] );
			if ( ! $user_ids ) {
				// no user ids
				return PHP_INT_MAX;
			}
		}

		echo implode( ",", $user_ids );

	}

}