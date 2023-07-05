<?php

namespace Gloo\Modules\Interactor_Cookies;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use Gloo\Modules\Interactor\Module as Interactor;

class Settings {

	private $prefix = 'gloo_interactor_cookies_';

	public function __construct() {
		add_action( 'elementor/element/before_section_end', [ $this, 'add_settings' ], 10, 2 );
		add_action( 'gloo/modules/interactor/trigger_loop_item', [ $this, 'check_datalayer_settings' ] );

		if(!is_admin()){
			add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}else{
			// add javascript and css to wp-admin dashboard.
			// add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}

	}
 
	public function check_datalayer_settings( $trigger ) {

		if ( ! isset( $trigger[ $this->prefix . 'triggers' ] ) || ! $trigger[ $this->prefix . 'triggers' ] ) {
			return;
		}

		$cookies_items = $trigger[ $this->prefix . 'triggers' ];
		$code = '';
		foreach ( $cookies_items as $cookie_item ) {
			$code .= $this->generate_js_code( $cookie_item );
		}
		add_filter( "gloo/modules/interactor/trigger_loop_item/trigger_functions/{$trigger['_id']}", function () use ( $code ) {
			return $code;
		} );
	}

	public function generate_js_code( $cookie_item ) {

		$interactor_settings       = Interactor::instance()->settings;
		$current_document_settings = $interactor_settings->get_current_document()->get_settings_for_display();

		if ( ! isset( $current_document_settings[ $this->prefix ] ) ) {
			return;
		}
		$cookies_items = $current_document_settings[ $this->prefix ];
		$variables       = $current_document_settings['gloo_interactor_variables'];
		
		$zapier_js = '';
		foreach ( $cookies_items as $single_cookie_item ) {
			if($single_cookie_item['_id'] != $cookie_item)
				continue;
			$variable_output = [];
			foreach ( $single_cookie_item[ $this->prefix . 'interactor_variables' ] as $zapier_variable ) {
				$var_key = array_search( $zapier_variable, array_column( $variables, '_id' ) );
				if ( $var_key === false ) {
					continue;
				}

				$current_var = $variables[ $var_key ];
				if ( ! $current_var['gloo_interactor_variable_name'] || ! $current_var['gloo_interactor_variable_value'] ) {
					continue;
				}
				$variable_output[ $current_var['gloo_interactor_variable_name'] ] = $current_var['gloo_interactor_variable_value'];
			}

			if ( ! $variable_output ) {
				continue;
			}
			

			$cookie_name = $single_cookie_item['gloo_interactor_cookies_title'];
			$cookie_type = $single_cookie_item['gloo_interactor_cookies_type'];

			// $zapier_js .= "gloo_cookie_name = '".$cookie_name."';";
			$zapier_js .= "set_interactor_cookies('".$cookie_name."', ".json_encode($variable_output).", '".$cookie_type."');";
		}

		return $zapier_js;
	}

	public function add_settings( $element, $section_id ) {

		if ( $section_id !== 'gloo_interactor_' ) {
			return;
		}

		$interactor_settings = Interactor::instance()->settings;

		// add data layer to interactor triggers
		$interactor_triggers = $element->get_controls( 'gloo_interactor_triggers' );

		$interactor_triggers['fields'][ $this->prefix . 'triggers' ] = [
			'label'    => __( 'Cookies to save', 'gloo' ),
			'type'     => \Elementor\Controls_Manager::SELECT2,
			'options'  => $interactor_settings->get_settings_as_options( $this->prefix, $this->prefix . 'title' ),
			'name'     => $this->prefix . 'triggers',
			'multiple' => true,
		];
		$element->update_control( 'gloo_interactor_triggers', $interactor_triggers );

		$zapier_repeater = new Repeater();

		$output_option = [
      'local_storage'      => 'Local Storage',
			'cookie'      => 'Cookies',
 			'session' => 'Session',
		];

		$zapier_repeater->add_control(
			$this->prefix . 'title',
			[
				'label'       => __( 'Cookie ID', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				// 'placeholder' => __( 'Name', 'gloo_for_elementor' ),
			]
		);

		
		$zapier_repeater->add_control(
			$this->prefix . 'type',
			[
				'type'     => Controls_Manager::SELECT,
				'label'    => __( 'Cookie Type', 'gloo_for_elementor' ),
				'options'  => $output_option,
				'default' => 'cookie',
			]
		);

		$zapier_repeater->add_control(
			$this->prefix . 'interactor_variables',
			[
				'type'     => Controls_Manager::SELECT2,
				'label'    => __( 'Variables', 'gloo_for_elementor' ),
				'options'  => $interactor_settings->get_variables(),
				'multiple' => true,
			]
		);

		$element->add_control(
			$this->prefix,
			[
				'label'         => __( 'Cookies Connector', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $zapier_repeater->get_controls(),
				'title_field'   => '{{{ ' . $this->prefix . 'title }}}',
			]
		);
	}

	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    if(!is_admin()){
      $script_abs_path = gloo()->plugin_path( 'includes/modules/interactor_cookies/assets/frontend/js/script.js');
      wp_register_script( $this->prefix.'js', gloo()->plugin_url().'includes/modules/interactor_cookies/assets/frontend/js/script.js', array('jquery'), $this->get_file_time($script_abs_path));
      wp_enqueue_script( $this->prefix.'js' );
    }
  }// wp_admin_style_scripts

	public function get_file_time($file){
    return date("ymd-Gis", filemtime( $file ));
  }

}
