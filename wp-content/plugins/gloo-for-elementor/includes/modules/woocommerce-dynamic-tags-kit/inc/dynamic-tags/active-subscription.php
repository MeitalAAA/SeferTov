<?php
namespace Gloo\Modules\WooCommerce_Dynamic_Tags_Kit;

Class ActiveSubscriptions extends \Elementor\Core\DynamicTags\Tag {

  public $prefix = 'gloo_active_subscriptions_tag';
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
		return 'woocoommerce-active-subscriptions';
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
		return __( 'Active Subscriptions', 'gloo_for_elementor' );
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
			$this->prefix().'user_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => array(
					'current_user' => __( 'Current User', 'gloo_for_elementor' ),
					// 'current_author' => __( 'Current Author', 'gloo_for_elementor' ),
					'queried_user' => __( 'Queried User', 'gloo_for_elementor' ),
				),
			)
		);

    $output_option = [
			// 'type_ul'      => 'Ul Structure',
			// 'type_ol'      => 'Ol Structure',
			'type_limeter' => 'Delimeter',
			// 'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		$this->add_control(
			$this->prefix().'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_array',
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
					$this->prefix().'field_output' => 'one_per_line'
				],
			)
		);

    $this->add_control(
			$this->prefix().'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
        'default' => ', ',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix().'field_output',
							'operator' => '==',
							'value' => 'type_limeter'
						],
					]
				]
			)
		);


    $this->add_control(
			$this->prefix().'data_index',
			array(
				'label'   => __( 'Array Index', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'first_index',
				'options' => array(
					'specific_index' => __( 'Specific Index', 'gloo_for_elementor' ),
					'first_index' => __( 'First Index', 'gloo_for_elementor' ),
					'last_index' => __( 'Last Index', 'gloo_for_elementor' ),
				),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix().'field_output',
							'operator' => '==',
							'value' => 'type_array'
						],
					]
				]
			)
		);


    $this->add_control(
			$this->prefix().'array_index',
			array(
				'label'     => __( 'Index Value', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->prefix().'field_output',
							'operator' => '==',
							'value' => 'type_array'
						],
						[
							'name' => $this->prefix().'data_index',
							'operator' => '==',
							'value' => 'specific_index'
						],
					]
				]
			)
		);

	}


	public function render() {

		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

    $context = $this->get_settings( $this->prefix().'user_context' );
    $data = [];

    if ( empty( $context ) && function_exists('jet_engine') ) {
			return;
		}

    switch ( $context ) {

			case 'current_user':
				$user_object = wp_get_current_user();
				break;

			// case 'current_author':
			// 	$user_object = jet_engine()->listings->data->get_current_author_object();
			// 	break;

			case 'queried_user':
				$user_object = jet_engine()->listings->data->get_queried_user_object();
				// $user_object = apply_filters( 'jet-engine/elementor/dynamic-tags/user-context-object/' . $context, $user_object );
				break;
		}




    if(isset($user_object) && !empty($user_object) && isset($user_object->ID)) {
      $current_user_id = $user_object->ID;
      if($current_user_id && function_exists('wcs_user_has_subscription') && wcs_user_has_subscription($current_user_id)){
      
        $user_subscriptions = wcs_get_users_subscriptions($current_user_id);
        if($user_subscriptions && is_array($user_subscriptions) && count($user_subscriptions) >= 1){
          foreach($user_subscriptions as $subscription){
            $data[] = $subscription->get_status();
          }
        }
      }
      
      echo $this->get_rendered_output($data);
    }

    // echo 'test';
	}

  /******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.



  public function get_rendered_output( $data = array() ) {		
		
		$field_output = $this->get_settings( $this->prefix().'field_output' );
		$delimiter = $this->get_settings( $this->prefix().'delimiter' );
		$data_index = $this->get_settings( $this->prefix().'data_index' );
		$array_index = $this->get_settings( $this->prefix().'array_index' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

    // return '';
    // db($field_output);
    // db($delimiter);
    // db($data_index);
    // db($array_index);
    // exit();
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
      // db($output);exit();
			return $output;
		}
	}

}