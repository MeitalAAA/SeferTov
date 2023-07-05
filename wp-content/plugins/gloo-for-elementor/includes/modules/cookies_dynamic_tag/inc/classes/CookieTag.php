<?php
namespace Gloo\Modules\CookiesDynamicTag;

Class CookieTag extends \Elementor\Core\DynamicTags\Tag {

	public $form_cookies_inputs;

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
		return 'gloo-cookie-dynamic-tag';
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
		return __( 'Cookie Dynamic Tag', 'gloo_for_elementor' );
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
			'cookie_id',
			array(
				'label'   => __( 'Cookie ID', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				// 'placeholder' => __( 'Type field key here', 'gloo_for_elementor' ),
			)
		);
    $output_option = [
      'local_storage'      => 'Local Storage',
			'cookie'      => 'Cookies',
 			'session' => 'Session',
		];
		$this->add_control(
			'is_php_cookie',
			[
				'label' => __( 'Is PhP Cookie?', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo' ),
				'label_off' => __( 'No', 'gloo' ),
				'return_value' => 'yes',
			]
		);
    $this->add_control(
			'cookie_type',
			[
				'label' => __( 'Cookie Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'local_storage',
				'options' => $output_option,
				'condition' => [
          'is_php_cookie!' => 'yes',
				],
				// 'label_block' => true,
				// 'separator' => 'before',
			]
		);

		$this->add_control(
			'php_cookie_type',
			[
				'label' => __( 'Cookie Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'cookie',
				'options' => [
					// 'local_storage'      => 'Local Storage',
					'cookie'      => 'Cookies',
					 'session' => 'Session',
				],
				'condition' => [
          'is_php_cookie' => 'yes',
				],
				// 'label_block' => true,
				// 'separator' => 'before',
			]
		);

		$this->add_control(
			'is_form_input',
			[
				'label' => __( 'Is form Input?', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo' ),
				'label_off' => __( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		
    $this->add_control(
			'is_array',
			[
				'label' => __( 'Is array?', 'gloo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'gloo' ),
				'label_off' => __( 'No', 'gloo' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
 			'type_delimeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_specific_array_index'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];

		
    
		$this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_ul',
				'options' => $output_option,
        'condition' => [
          'is_array' => 'yes',
				],
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
        'default' => ', ',
				'condition' => [
					'field_output' => 'type_delimeter',
          'is_array' => 'yes',
				],
			)
		);

		$this->add_control(
			'data_index',
			array(
				'label'   => __( 'Array Index', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'specific_index',
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
							'value' => 'type_specific_array_index'
						],
            [
							'name' => 'is_array',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Index ID', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'min'       => 0,
				'max'       => 1000,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'field_output',
							'operator' => '==',
							'value' => 'type_specific_array_index'
						],
						[
							'name' => 'data_index',
							'operator' => '==',
							'value' => 'specific_index'
            ],
            [
							'name' => 'is_array',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			)
		);
		
		// $this->add_control(
		// 	'enable_zero',
		// 	[
		// 		'label' => __( 'Enable Zero', 'gloo' ),
		// 		'description' => __( 'if empty return 0', 'gloo' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'label_on' => __( 'Yes', 'gloo' ),
		// 		'label_off' => __( 'No', 'gloo' ),
		// 		'return_value' => 'yes',
		// 		'default' => 'yes',
    //     'condition' => [
    //       'is_array' => 'yes',
		// 		],
		// 	]
		// );

	}


	public function render() {
		$output = '';
		wp_enqueue_script('gloo_cookie_dynamic_tag' );
		$cookie_id = $this->get_settings( 'cookie_id' );
    $cookie_type = $this->get_settings( 'cookie_type' );
    $is_array = $this->get_settings( 'is_array' );
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		// $enable_zero = $this->get_settings( 'enable_zero' );
		$array_index     = $this->get_settings( 'array_index' );
		$data_index = $this->get_settings( 'data_index' );
		$is_php_cookie = $this->get_settings( 'is_php_cookie' );
		$php_cookie_type = $this->get_settings( 'php_cookie_type' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );
		$is_form_input = $this->get_settings( 'is_form_input' );
		$fall_back_value = $this->get_settings('fallback');


		if(!empty($cookie_id)) {
      $settings_array = array(
        'cookie_id' => $cookie_id,
        'cookie_type' => $cookie_type,
        'is_array' => $is_array,
        'field_output' => $field_output,
        'delimiter' => $delimiter,
        // 'enable_zero' => $enable_zero,
        'array_index' => $array_index,
        'data_index' => $data_index,
		'is_php_cookie' => $is_php_cookie,
		'Fallback' => $fall_back_value,
      );

			if($is_php_cookie == 'yes' && !empty($php_cookie_type) && !empty($cookie_id)){
				
				if($php_cookie_type == 'cookie' && isset($_COOKIE[$cookie_id]))
					$output = $_COOKIE[$cookie_id];
				else if($php_cookie_type == 'session' && isset($_SESSION[$cookie_id]))
					$output = $_SESSION[$cookie_id];
				
				if($is_array == 'yes' /*&& is_array($output)*/ && $field_output && !empty($field_output)){
					$output = json_decode(wp_kses_stripslashes($output), true);
					if($output && is_array($output) && count($output) >= 1){
						if($field_output == 'type_specific_array_index' && !empty($data_index)){
							if($data_index == 'first_index'){
								$firstKey = array_key_first($output);
								$output = $output[$firstKey];
							}else if($data_index == 'last_index'){
								$output = end($output);
							}
							else if($data_index == 'specific_index' && !empty($array_index) && isset($output[$array_index])){
								$output = $output[$array_index];
							}
						}
						else if($field_output == 'type_delimeter' && !empty($delimiter)){
							$output = implode( $delimiter, $output );
						}
						else if ( $field_output == 'type_ul' ) {
							$output_html = '<ul class="">';
							foreach ( $output as $value ) {
								$output_html .= '<li>' . $value . '</li>';
							}
							$output_html .= '</ul>';
							$output = $output_html;
						} else if ( $field_output == 'type_ol' ) {
							$output_html = '<ol class="">';
							foreach ( $post_data as $value ) {
								$output_html .= '<li>' . $value . '</li>';
							}
							$output_html .= '</ol>';
							$output = $output_html;
						}
						else if ( $field_output == 'one_per_line' ) {
							if($one_per_line_type == 'html')
								$output = implode( '<br />', $terms_data );
							else
								$output = implode( PHP_EOL, $terms_data );
		
						}
						else if ( $field_output == 'type_lenght' ) {
								$output = count($output);
		
						}
					}
					
				}
			}else{
				// $this->set_render_attribute('_wrapper', 'class', 'gloo_cookie_dynamic_tag');
				// $this->set_render_attribute('_wrapper', 'data-settings', json_encode($settings_array));
				// $is_form_input = 'yes';
				if($is_form_input == 'yes'){
					$this->form_cookies_inputs = $settings_array;
					echo $this->get_gloo_cookie_id();
					add_action('wp_footer', function(){ ?>
						<script id="gloo_cookie_inline_js">
							if(typeof gloo_input_cookies_object != 'object')
								var gloo_input_cookies_object = [];
								
								gloo_input_cookies_object['<?php echo $this->get_gloo_cookie_id(); ?>'] = <?php echo json_encode($this->form_cookies_inputs); ?>;
						</script>
					<?php });
				}
				else
					$output = "<div class='gloo_cookie_dynamic_tag' data-settings='".json_encode($settings_array)."'>{$output}</div>";
			}
			
      echo $output;
		}
	
 	}

	public function get_gloo_cookie_id(){
		return 'gloo_'.preg_replace('#[^a-zA-Z_]#', '', $this->form_cookies_inputs['cookie_id']).'_'.$this->form_cookies_inputs['cookie_type'].'_'.$this->form_cookies_inputs['array_index'].'_'.preg_replace('#[^a-zA-Z_]#', '', $this->form_cookies_inputs['data_index']);
	}
	
}