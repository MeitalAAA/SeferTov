<?php
namespace Gloo\Modules\Random_String_Dynamic_Tag;

Class Random_String_Tag extends \Elementor\Core\DynamicTags\Tag {

	private $prefix = 'rst_';
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
		return 'random-string-tag';
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
		return __( 'Random String Tag', 'gloo_for_elementor' );
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
			$this->prefix.'string_prefix',
			array(
				'label'   => __( 'String Prefix', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'String Prefix', 'gloo_for_elementor' ),
			)
		);
 
		$options = [
			'numbers'      => 'Only numbers',
			'uppercase_character'      => 'Only uppercase characters',
 			'lowercase_charachter' => 'Only lowercase charachters',
			'any'  => 'Any charachter',
 		];
    
		$this->add_control(
			$this->prefix.'return_output',
			array(
				'label'   => __( 'Return Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'numbers',
				'options' => $options,
			)
		);

		$this->add_control(
			$this->prefix.'str_length',
			[
				'label' => esc_html__( 'String Length', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 10,
			]
		);
 	}

	function generateRandomString( $length = 10, $output_type) {
	
		if($output_type == 'uppercase_character') {
			$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		} elseif($output_type == 'lowercase_charachter') {
			$characters = 'abcdefghijklmnopqrstuvwxyz';
		} elseif($output_type == 'numbers') {
			$characters = '0123456789';
		} else {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	public function render() {
		$settings  = $this->get_settings_for_display();

		if(isset($settings[$this->prefix.'return_output']) && !empty($settings[$this->prefix.'return_output'])) {

			$length = $settings[$this->prefix.'str_length'];
			$string_prefix = $settings[$this->prefix.'string_prefix'];
			$output_type = $settings[$this->prefix.'return_output'];

			$rand_string = $this->generateRandomString($length, $output_type);

			if(!empty($string_prefix)) {
				$rand_string = $string_prefix.$rand_string;
			}

			echo $rand_string;
		}
 	}
}