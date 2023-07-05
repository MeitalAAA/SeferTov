<?php
namespace Gloo\Modules\Community_Dynamic_Tags;

Class Friends_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'bp-friends-tag';
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
		return __( 'BP Friends', 'gloo_for_elementor' );
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
			'gloo_bp_friends_note',
			[
                'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Note: it will return BP friends', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
        );

	}

	public function render() {
	
		if ( ! function_exists( 'friends_get_friend_user_ids' ) ) {
			return PHP_INT_MAX;
		}
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return PHP_INT_MAX;
		}
		$ids = friends_get_friend_user_ids( $user_id );

		if ( ! $ids ) {
			return PHP_INT_MAX;
		}

		echo implode( ",", $ids );

	}

}