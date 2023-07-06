<?php

namespace Gloo\Modules\Learndash_Dynamic_Tags;

class My_Courses_Ids extends \Elementor\Core\DynamicTags\Tag {

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
		return 'gloo-mycourses';
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
		return __( 'My Courses', 'gloo_for_elementor' );
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
		
		/* output options */
		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_limeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line',
 		];

		$this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_none',
				'options' => $output_option,
 			)
		);

 
		$this->add_control(
			'one_per_line_type',
			array(
				'label'     => __( 'Line Break Type', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default' => 'php',
				'options' => array('php' => 'PhP', 'html' => 'HTML'),
				'condition' => [
					'field_output' => 'one_per_line'
				],
			)
		);
 
		$this->add_control(
			'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_output',
							'operator' => '==',
							'value' => 'type_limeter'
						],
					]
				]
			)
		);

		$this->add_control(
			'data_index',
			array(
				'label'   => __( 'Array Index', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
 				'options' => array(
					'specific_index' => __( 'Specific Index', 'gloo_for_elementor' ),
					'first_index' => __( 'First Index', 'gloo_for_elementor' ),
					'last_index' => __( 'Last Index', 'gloo_for_elementor' ),
				),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_output',
							'operator' => '==',
							'value' => 'type_array'
						],
					]
				]
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Index Value', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_output',
							'operator' => '==',
							'value' => 'type_array'
						],
						[
							'name' => 'data_index',
							'operator' => '==',
							'value' => 'specific_index'
						],
					]
				]
			)
		);

	}

	public function get_rendered_output( $data = array() ) {		
		
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$data_index = $this->get_settings( 'data_index' );
		$array_index = $this->get_settings( 'array_index' );

		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		$settings = $this->get_settings_for_display();

		if( !empty( $data ) && is_array( $data ) ) {
			// echo '<pre>'; print_r($data); echo '</pre>';
			$output .= '';

			if ( $field_output == 'type_ul' ) {

				$output .= '<ul class="tax-ul">';

				foreach ( $data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ul>';

			} else if ( $field_output == 'type_ol' ) {

				$output .= '<ol class="tax-ol">';

				foreach ( $data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ol>';


			} else if ( $field_output == 'type_lenght' ) {

				$output = count( $data );

			} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

				$output = implode( $delimiter, $data );

			} else if ( $field_output == 'type_array' ) {
				
				if( $data_index == 'specific_index'  && is_numeric($array_index) ) {
					if ( isset( $data[ $array_index ] ) && ! empty( $data[ $array_index ] ) ) {
						$output = $data[ $array_index ];
					}
				} elseif( $data_index ==  'first_index' ) {
					
					$firstKey = array_key_first($data);
					$output = $data[$firstKey];

				} elseif( $data_index ==  'last_index' ) {
					$output = end($data);
				}
			} else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $data );
				else
					$output = implode( PHP_EOL, $data );

			}  

			return $output;
		}
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
					$user_id = get_post_field( 'post
					_author', $post_id );
  				}
				 
				break;
		}
	  
		if ( function_exists( 'learndash_user_get_enrolled_courses' ) && $user_id ) {
			$user_course_ids = learndash_user_get_enrolled_courses( $user_id );
			
			if ( !empty( $user_course_ids ) && is_array( $user_course_ids ) ) {
				echo $this->get_rendered_output($user_course_ids);
			} else {
				echo '0';
			}
		}
  	}
}