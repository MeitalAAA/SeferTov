<?php
namespace Gloo\Modules\Affiliate_Dynamic_Tags;

Class Affiliate_Tag extends \Elementor\Core\DynamicTags\Tag {

	private $prefix = 'gloo_affiliate_';
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
		return 'gloo-wp-affiliate-tag';
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
		return __( 'AffiliateWP Dynamic Tag', 'gloo_for_elementor' );
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
			$this->$prefix.'affiliate_field',
			array(
				'label'   => __( 'Affiliate Field', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ID',
				'options' => array(
					'ID' => __( 'Affiliate ID', 'gloo_for_elementor' ),
					'name' => __( 'Name', 'gloo_for_elementor' ),
					'url'=> __( 'URL', 'gloo_for_elementor' ),
					'reg_date' => __( 'Registration Date', 'gloo_for_elementor' ),
					'status' => __( 'Status', 'gloo_for_elementor' ),
					'user_id' => __( 'User ID', 'gloo_for_elementor' ),
					'user_name' => __( 'Username', 'gloo_for_elementor' ),
				),
			)
		);

		
		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_delimeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];
 
		$this->add_control(
			$this->$prefix.'field_output',
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
					$this->$prefix.'field_output' => 'one_per_line'
				],
			)
		);

		$this->add_control(
			$this->$prefix.'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->$prefix.'field_output' => 'type_delimeter'
				]
			)
		);

		$this->add_control(
			$this->$prefix.'data_index',
			array(
				'label'   => __( 'Array Index', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => array(
					'specific_index' => __( 'Specific Index', 'gloo_for_elementor' ),
					'first_index' => __( 'First Index', 'gloo_for_elementor' ),
					'last_index' => __( 'Last Index', 'gloo_for_elementor' ),
				),
				'condition' => [
					$this->$prefix.'field_output' => 'type_array'
				]
			)
		);

		$this->add_control(
			$this->$prefix.'array_index',
			array(
				'label'     => __( 'Index Value', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'condition' => [
					$this->$prefix.'field_output' => 'type_array',
					$this->$prefix.'data_index' => 'specific_index'
				]
			)
		);
	}

	public function get_rendered_output( $data = array() ) {		
		$settings = $this->get_settings_for_display();

		$field_output = $settings[$this->$prefix.'field_output'];
		$delimiter = $settings[$this->$prefix.'delimiter'];
		$data_index = $settings[$this->$prefix.'data_index'];
		$array_index = $settings[$this->$prefix.'array_index'];
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );
	
		if( !empty( $data ) && is_array( $data ) ) {
			
			$output .= '';

			if ( $field_output == 'type_ul' ) {

				$output .= '<ul>';

				foreach ( $data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ul>';

			} else if ( $field_output == 'type_ol' ) {

				$output .= '<ol>';

				foreach ( $data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ol>';


			} else if ( $field_output == 'type_lenght' ) {

				$output = count( $data );

			} else if ( $field_output == 'type_delimeter' && ! empty( $delimiter ) ) {

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
			}else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $data );
				else
					$output = implode( PHP_EOL, $data );

			}

			echo $output;
		}
	}

	public function render() {
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );
		$settings = $this->get_settings_for_display();
		$field = $settings['affiliate_field'];
		
		$all_affiliates = affiliate_wp()->affiliates->get_affiliates(array(
			'number' => -1
		));
 
		$affiliate_data = array();

		if(!empty($all_affiliates)) {
			foreach( $all_affiliates as $affiliate) {

				if($field == 'ID') {
					$affiliate_data[] = $affiliate->affiliate_id;
				} else if($field == 'name') {
					$affiliate_data[] = affwp_get_affiliate_name($affiliate->affiliate_id);
				} else if($field == 'url') {
					$affiliate_data[] = affwp_get_affiliate_referral_url(array(
						'affiliate_id' => $affiliate->affiliate_id
					));
				} else if($field == 'reg_date') {
					$affiliate_data[] = $affiliate->date_registered;
				} else if($field == 'status') {
					$affiliate_data[] = $affiliate->status;
				} else if($field == 'user_id') {
					$affiliate_data[] = $affiliate->user_id;
				} else if($field == 'user_name') {
					$user = get_user_by( 'id', $affiliate->user_id ); 
					$affiliate_data[] = $user->user_login;
				}
			}

			$this->get_rendered_output($affiliate_data);
		}
	}
}