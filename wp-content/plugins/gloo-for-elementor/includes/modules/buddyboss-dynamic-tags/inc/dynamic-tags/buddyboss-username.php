<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class BuddyBoss_Username extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-buddyboss-username';
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
		return __( 'Dynamic Username', 'gloo_for_elementor' );
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
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY
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

		$context_options = array(
			'current_user' => __( 'Current User', 'gloo_for_elementor' ),
			'displayed_user' => __( 'BB Displayed User', 'gloo_for_elementor' )
 		);

		if(function_exists('jet_engine')) {
			$context_options['queried_user'] = __( 'Queried User', 'gloo_for_elementor' );
		}

		$this->add_control(
			'user_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => $context_options,
			)
		);

	}

	public function render() {

		$context = $this->get_settings( 'user_context' );

		if ( ! $context ) {
			$context = 'current_user';
		}

		$value = false;


		if ( 'current_user' === $context ) {
			$user_object = wp_get_current_user();
			
		} elseif( 'displayed_user' === $context ) {
			$bp = buddypress();
			$id = ! empty( $bp->displayed_user->id )? $bp->displayed_user->id: 0;

			$user = get_user_by('ID', $id);
			$user_object = $user->data;

		} else {

			if(function_exists('jet_engine')) {
				$user_object = jet_engine()->listings->data->get_current_user_object();
			}
		}

		if( !empty( $user_object->ID )) {
			$value = bp_core_get_username( $user_object->ID );
			echo $value;
		}
		
	}

}