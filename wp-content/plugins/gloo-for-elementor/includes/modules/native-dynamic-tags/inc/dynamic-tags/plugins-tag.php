<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

Class Plugins_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'plugins-dynamic-tags';
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
		return __( 'Plugins Dynamic Tag', 'gloo_for_elementor' );
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

		$plugins = [];
		
		if ( function_exists( 'get_plugins' ) ) {
			$all_plugins = get_plugins();
			
			if(!empty($all_plugins)) {
				foreach($all_plugins as $plugin_file => $plugin) {
					$plugins[$plugin_file] = $plugin['Name'];
				}
			}
		}

		$this->add_control(
			'plugin_name',
			array(
				'label'   => __( 'Plugin', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $plugins,
			)
		);

		$return = [
			'name' => 'Name',
			'version' => 'Version',
			'author' => 'Author',
			'description' => 'Description'
		];

		$this->add_control(
			'return_plugin_value',
			array(
				'label'   => __( 'Return Value', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $return,
			)
		);

	}

  	public function render() {
	  
		$settings = $this->get_settings_for_display();
		
		if( isset($settings['plugin_name']) && !empty($settings['plugin_name'])) {

		$plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/'.$settings['plugin_name']);
  
		if( isset($settings['return_plugin_value']) && !empty($settings['return_plugin_value']) ) {
			  switch($settings['return_plugin_value']) {
				  case 'name': 
					  $output = $plugin_data['Name'];
					  break;
				  case 'version': 
					  $output = $plugin_data['Version'];
					  break;
				  case 'author': 
					  $output = $plugin_data['Author'];
					  break;
				  case 'description': 
					  $output = $plugin_data['Description'];
					  break;
			  }

			  echo $output;
		  }
	  }
	  
   }
}
