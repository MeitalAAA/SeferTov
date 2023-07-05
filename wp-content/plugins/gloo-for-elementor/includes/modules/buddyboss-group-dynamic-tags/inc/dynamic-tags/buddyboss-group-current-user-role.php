<?php
namespace Gloo\Modules\BB_Group_Dynamic_Tags;

class BuddyBoss_Group_User_Role extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddyboss-group-user-role';
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
		return __( 'BuddyBoss Group Current User Role', 'gloo_for_elementor' );
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
			'group_user_value',
			array(
				'label'   => __( 'Return Value', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_group',
				'options' => [
					'role_lable' => 'Role Lable',
					'role_id' => 'Role ID'
				],
			)
		);

		$this->add_control(
			'group_name_note',
			[
				'label' => __( 'Note', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'This will return current user role in current group', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

	}

	public function render() {
		
		if ( ! is_user_logged_in() ) {	
			return false;
		}

		$user_id = get_current_user_id();
		$current_group = groups_get_current_group();
		$group_id = bp_get_group_id( $current_group );

		if ( ! groups_is_user_member( $user_id, $group_id ) ) {
			return false;
		}

		$group_user_value = $this->get_settings( 'group_user_value' );
		
		if( $group_user_value == 'role_lable' ) {
			echo bp_get_user_group_role_title( $user_id, $group_id );
		} elseif ($group_user_value == 'role_id' ) {
			$value = '';
			if(groups_is_user_admin( $user_id, $group_id )) {
				$value = 'admin';
			} elseif(groups_is_user_mod( $user_id, $group_id )) {
				$value = 'mod';
			} elseif(groups_is_user_member( $user_id, $group_id )) {
				$value = 'member';
			}

			echo $value;
		}
	}
}