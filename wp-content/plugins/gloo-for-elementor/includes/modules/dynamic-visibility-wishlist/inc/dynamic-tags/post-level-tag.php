<?php
namespace Gloo\Modules\Dynamic_Visibility_Wishlist;

Class Post_Level_Tag extends \Elementor\Core\DynamicTags\Tag {
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
		return 'gloo-post-level-tag';
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
		return __( 'Current Post Whisleist Levels', 'gloo_for_elementor' );
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
		
		$level_options = [];

		if(function_exists('wlmapi_get_levels')) {

			$levels = wlmapi_get_levels();
			$levels = isset( $levels['levels']['level'] ) ? $levels['levels']['level'] : [];

			foreach ( $levels as $key => $value ) {
				$level_options[$value['id']] = $value['name'];
			}
		}

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

		$output_option = [	
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_limiter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		$this->add_control(
			$this->prefix.'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
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
					$this->prefix.'field_output' => 'one_per_line'
				],
			)
		);

		$this->add_control(
			$this->prefix.'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix.'field_output' => 'type_limiter'
				],
			)
		);

		$this->add_control(
			$this->prefix.'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'condition' => [
					$this->prefix.'field_output' => 'type_array'
				],
			)
		);

		$this->add_control(
			$this->prefix.'custom_roles',
			[
				'label' => __( 'Current Post', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'your-plugin' ),
				'label_off' => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			$this->prefix.'selected_roles',
			[
				'label' => __( 'Levels', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $level_options,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix.'custom_roles',
							'operator' => '!=',
							'value' => 'yes'
						],
					]
				]
			]
		);
	}

	public function get_user_level() {
		$levels = array();
		
		if ( is_user_logged_in() ) {
			
			$user_id = get_current_user_id();
			$user_levels = wlmapi_get_member_levels( $user_id );

		
			if(!empty($user_levels)) {
				foreach($user_levels as $level) {
					$levels[] = $level->Level_ID;
				}
			}
		}

		return $levels;
	}

	public function render() {

		$current_post = $this->get_settings( $this->prefix.'custom_roles' );
		$selected_roles = $this->get_settings( $this->prefix.'selected_roles' );
		
		$field_output = $this->get_settings( $this->prefix.'field_output' );
		$return_value = $this->get_settings( $this->prefix.'return_value' );
		$delimiter = $this->get_settings( $this->prefix.'delimiter' );
		$array_index = $this->get_settings( $this->prefix.'array_index' );

		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		$levels = array();
		$post_id = get_the_id();
 
		if($current_post == 'yes') {
			$wpm_access = wishlistmember_instance()->GetContentLevels(get_post_type(), $post_id); 
		} else {
			$wpm_access = $selected_roles;
		}
		
		if(!empty($wpm_access)) {
			foreach($wpm_access as $level_id) {

				if($level_id != 'Protection') {
					$level = wlmapi_get_level($level_id);

					if(!empty($level)) {
						if($return_value == 'title') {
							$levels[] =  $level['level']['name'];
						} else {
							$levels[] =  $level['level']['id'];
						}
					}
				}
			}
		}

 

 		$output = '';

		if(!empty($levels) && is_array($levels)) {

			if ( $field_output == 'type_ul' ) {

				$output .= '<ul class="tax-ul">';

				foreach ( $levels as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ul>';

			} else if ( $field_output == 'type_ol' ) {

				$output .= '<ol class="tax-ol">';

				foreach ( $levels as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ol>';


			} else if ( $field_output == 'type_lenght' ) {

				$output = count( $levels );

			} else if ( $field_output == 'type_limiter' && ! empty( $delimiter ) ) {

				$output = implode( $delimiter, $levels );

			} else if ( $field_output == 'type_array' && is_numeric($array_index) ) {

				if ( isset( $levels[ $array_index ] ) && ! empty( $levels[ $array_index ] ) ) {
					$output = $levels[ $array_index ];
				}

			}
			else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $levels );
				else
					$output = implode( PHP_EOL, $levels );

			}

			echo $output;

		}
	
	//	$level = new \WLMAPI();
	//	$levels = $level->GetPostLevels(get_the_id());
		// echo '<pre>';
		// print_r($res_access);

 		// //print_r($levels);
		// echo '</pre>';

		// if(is_array($custom_roles)) {
		// 	echo implode(',',$custom_roles);
		// }
	 
	}
}
