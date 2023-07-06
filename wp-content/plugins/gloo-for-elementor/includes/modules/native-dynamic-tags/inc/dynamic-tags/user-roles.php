<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

Class User_Role_Dynamic_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'user-role-dynamic-tag';
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
		return __( 'User Role Tag', 'gloo_for_elementor' );
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
			'user_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => array(
					'current_user' => __( 'Current User', 'gloo_for_elementor' ),
					'current_author' => __( 'Current Author', 'gloo_for_elementor' ),
					'queried_user' => __( 'Queried User', 'gloo_for_elementor' ),
				),
			)
		);

		$this->add_control(
			'return_value',
			array(
				'label'   => __( 'Return', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'role_label',
				'options' => array(
					'role_label' => __( 'Label', 'gloo_for_elementor' ),
					'role_value' => __( 'Value', 'gloo_for_elementor' ),
				),
			)
		);

		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_limeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		$this->add_control(
			'field_output',
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
				'default' => 'current_user',
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
	
		if( !empty( $data ) && is_array( $data ) ) {
			
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
			}
			else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $data );
				else
					$output = implode( PHP_EOL, $data );

			}

			echo $output;
		}
	}

	public function get_role_names() {

		global $wp_roles;
		
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();
		
		return $wp_roles->get_names();
	}
	
	public function render() {
		$context = $this->get_settings( 'user_context' );
		$return_value = $this->get_settings( 'return_value' );

		$data = [];
		
		if ( empty( $context ) && function_exists('jet_engine') ) {
			return;
		}

		switch ( $context ) {

			case 'current_user':
				$user_object = wp_get_current_user();
				break;

			case 'current_author':
				$user_object = jet_engine()->listings->data->get_current_author_object();
				break;

			case 'queried_user':
				$user_object = jet_engine()->listings->data->get_queried_user_object();
				$user_object = apply_filters( 'jet-engine/elementor/dynamic-tags/user-context-object/' . $context, $user_object );
				break;
		}

		if($return_value == 'role_label' || $return_value == 'role_value') { 

			if(!empty($user_object->roles) && is_array($user_object->roles)) {

				if($return_value == 'role_label') {
					$all_roles = $this->get_role_names();
	
					foreach( $user_object->roles as $role ) {
						$data[] = $all_roles[$role];
					}

				} else {
					$data = $user_object->roles;
				}
	
				return $this->get_rendered_output($data);
			}
		}
	}	
}