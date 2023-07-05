<?php

namespace Gloo\Modules\Learndash_Dynamic_Tags;

class Is_User_Enrolled_Course extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-enrolled-course';
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
		return __( 'Is User Enrolled Course', 'gloo_for_elementor' );
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
   
 		$tag_content = array(
			'current_post' => __( 'Current Post', 'gloo_for_elementor' ),
			'current_user' => __( 'Current User', 'gloo_for_elementor' ),
			'current_author' => __( 'Current Author', 'gloo_for_elementor' ),
			'queried_post_author' => __( 'Queried Post Author', 'gloo_for_elementor' ),
		);

		if(function_exists( 'jet_engine' )) {
			$tag_content['queried_user'] = __( 'Queried User', 'gloo_for_elementor' );
 		}

		$this->add_control(
			'tag_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => $tag_content
			)
		);
		
		$this->add_control(
			'course_enrolled_note',
			[
				'label' => esc_html__( 'Note', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Return 1 if user is enrolled to course else 0', 'gloo_for_elementor' ),
 			]
		);

 	}
 
	public function render() {
		$settings = $this->get_settings_for_display();
		$course_id = get_the_ID();
 
		$context = ($settings['tag_context']) ? $settings['tag_context'] : null;
		
		switch ( $context ) {
 			case 'current_user':
				$user_id = get_current_user_id();

 				break;

			case 'current_author':
				if(function_exists('jet_engine')) {
					$user_object = jet_engine()->listings->data->get_current_author_object();
				} else {
					$post_id =  get_the_ID();
					$user_id = get_post_field( 'post_author', $post_id );
 				}
 
				break;

			case 'queried_user_author':
				$post_id = get_the_ID();
				$post = get_post( $post_id );

				if ( $post ) {
					$user_id = get_the_author_meta( 'ID', $post->post_author );
				}

				break;

			case 'queried_user':
				if(function_exists('jet_engine')) {
					$user_object = jet_engine()->listings->data->get_queried_user_object();
					$user_object = apply_filters( 'jet-engine/elementor/dynamic-tags/user-context-object/' . $context, $user_object );
					$user_id = $user_object->ID;
				}  

				break;

 			case 'queried_post_author':
				if(function_exists('jet_engine')) {
					$object = jet_engine()->listings->data->get_current_object();

					if( !empty( $object ) ) {
						$user_id = get_the_author_meta( 'ID', $object->post_author );
 					}

				} else {
					$post_id =  get_the_ID();
					$user_id = get_post_field( 'post_author', $post_id );
  				}
				 
				break;
		}
	  
		$is_subscribed = 0;

		if ( function_exists( 'learndash_user_get_enrolled_courses' ) && $user_id ) {
			$user_course_ids = learndash_user_get_enrolled_courses( $user_id );
			
			if ( !empty($course_id) && ! empty( $user_course_ids ) && is_array($user_course_ids) ) {

				if ( in_array( $course_id, $user_course_ids ) ) {
					$is_subscribed = 1;
				}  
			}
		}

		echo $is_subscribed;
  	}

}