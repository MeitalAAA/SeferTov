<?php
namespace Gloo\Modules\BB_Group_Dynamic_Tags;

class BuddyBoss_Group_Name extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddyboss-group-name';
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
		return __( 'BuddyBoss Group Name', 'gloo_for_elementor' );
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
		
		$this->add_control(
			'group_return',
			array(
				'label'   => __( 'Return Value', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_group',
				'options' => [
					'group_slug' => 'Group Slug',
					'group_name' => 'Group Name'
				],
			)
		);

		$this->add_control(
			'group_name_note',
			[
				'label' => __( 'Note', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Tag will return group name/slug', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

	}

	public function render() {

		$current_group = groups_get_current_group();
		$group_return = $this->get_settings( 'group_return' );
		
		if( $group_return == 'group_slug' ) {
			echo urldecode(bp_get_group_slug( $current_group ));
 		} else if( $group_return == 'group_name' ) {
			bp_group_name(); 
		}
	}
}