<?php
namespace Gloo\Modules\Dynamic_Visibility_Wishlist;

Class User_level_Tag extends \Elementor\Core\DynamicTags\Tag {
	private $prefix = 'gloo_';
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
		return 'gloo-user-level-tag';
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
		return __( 'Current User Wishlist Levels', 'gloo_for_elementor' );
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
		

		$this->add_control(
			$this->prefix.'wishlist_roles',
			[
				'label' => __( 'Note', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Tag will return the current user wishlist membership level', 'gloo_for_elementor' ),
			]
		); 

		$return = array(
			'id' => 'ID',
			'title' => 'Title'
		);

		$this->add_control(
			$this->prefix.'return_value',
			array(
				'label'   => __( 'Return', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'id',
				'options' => $return,
			)
		);
		
	}

	public function render() {
		
		$return = $this->get_settings( $this->prefix.'return_value' );
		
		if ( is_user_logged_in() ) {
			
			$output = '';
			$user_id = get_current_user_id();
			$user_levels = wlmapi_get_member_levels( $user_id );

			if(!empty($user_levels)) {

				$wpm_access = wishlistmember_instance()->GetContentLevels(get_post_type(), get_the_id());
						
				if(!empty($wpm_access) && is_array($wpm_access)) {
					
					if(!empty($user_levels)) {
						
						foreach($user_levels as $level) {
						
							if(in_array($level->Level_ID, $wpm_access)) {

								if($return == 'title') {
								 	$output = $level->Name;
								} else {
									$output = $level->Level_ID;
								}
								break;
							}
						}
					}
				}

				echo $output;
			}
 
		}
 	 
	}
}
