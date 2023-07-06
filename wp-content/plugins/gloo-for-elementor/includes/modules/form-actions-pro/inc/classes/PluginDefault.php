<?php
namespace Gloo\Modules\Form_Actions_Pro;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginDefault extends Plugin{

	private static $instance = null;
	public $is_repeater = false;
	public $repeater_index = 0;

	
	
	/******************************************/
	/***** Single Ton base intialization of our class **********/
	/******************************************/
  public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){

    add_action( 'elementor/element/after_section_end', [ $this, 'after_section_end'], 11, 2);

		if(is_admin()){
			add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'editor_scripts' ) );
		}

		add_action('elementor_pro/forms/validation', [$this, 'elementor_pro_forms_validation'], PHP_INT_MAX, 2);
  
	}// construct function end here


	/******************************************/
	/***** editor_scripts **********/
	/******************************************/
	public function editor_scripts() {
		$script_abs_path = $this->plugin_path( 'assets/admin/js/script.js');
		wp_enqueue_script( $this->prefix('editor_script'), $this->plugin_url() . 'assets/admin/js/script.js', [ 'jquery' ], get_file_time($script_abs_path) );
	}
	

	/******************************************/
	/***** after_section_end **********/
	/******************************************/
	public function after_section_end($widget, $section_id){

		if ( 'section_integration' !== $section_id ) {
			return;
    }


		$actions = \ElementorPro\Modules\Forms\Module::instance()->get_form_actions();

		$actions_options = ['' => 'none'];

		foreach ( $actions as $action ) {
			$actions_options[ $action->get_name() ] = $action->get_label();
		}

		$widget->start_controls_section(
			'gloo_section_pro_form_actions',
			[
				'label' => __( 'Form Actions Pro', 'elementor-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			$this->prefix().'form_action', [
				'label' => __( 'After Form Action', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options' => $actions_options,
				'render_type' => 'none',
			]
		);
		/*$repeater->add_control(
			$this->prefix().'enable_conditions', [
				'label' => __( 'Conditional Logic', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			]
		);
		$repeater->add_control(
			$this->prefix().'condition_action', [
				'label' => __( 'Action', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'      => [
					''        => 'None',
					'execute'    => 'Run this action',
					'not_execute'    => 'Don\'t run this action',
				],
				'condition'   => [
					$this->prefix().'enable_conditions' => 'yes'
				],
			]
		);
		$repeater->add_control(
			$this->prefix().'condition_chain', [
				'label' => __( 'Condition Chain', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [],
				'condition'   => [
					$this->prefix().'enable_conditions' => 'yes'
				],
			]
		);
		$repeater->add_control(
			$this->prefix().'next_action', [
				'label' => __( 'Don\'t fire next if fails', 'gloo_for_elementor' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
				'condition'   => [
					$this->prefix().'enable_conditions' => 'yes'
				],
			]
		);*/
		$actions_options_json = json_encode($actions_options);
		$widget->add_control(
			$this->prefix().'form_actions_list',
			[
				'label'       => __( 'Actions After Submit', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '<# '."let actions_options = $actions_options_json;"."let actions_option = actions_options[".$this->prefix().'form_action'."]; ".'#>{{{actions_option}}}',
				'prevent_empty' => false,
			]
		);
		$widget->end_controls_section();
	}


	public function elementor_pro_forms_validation($record, $ajax_handler){
		
		$submit_actions = $record->get_form_settings( 'submit_actions' );
		$prefix = $this->prefix();

			if(empty($ajax_handler->errors)){

				
				$field_with_settings = $record->get_form_settings('form_fields');
				
				$settings = $record->get( 'form_settings' );
				$form_actions_list   = $settings[ $prefix . 'form_actions_list' ];

				// Get sumitetd Form data
				$raw_fields = $record->get( 'fields' );
				
				// Normalize the Form Data
				$fields = [];
				foreach ( $raw_fields as $id => $field ) {
					$fields[ $id ] = $field['value'];
				}
				
				if($form_actions_list && is_array($form_actions_list) && count($form_actions_list) >= 1){
					$break_actions = false;
					$record = apply_filters( 'elementor_pro/forms/record/actions_before', $record, $ajax_handler );
					foreach($form_actions_list as $form_action){
						if($break_actions == false){
							if ($submit_actions && isset($form_action[$prefix.'form_action']) && in_array( $form_action[$prefix.'form_action'], $submit_actions ) ) {
								$current_action_name = $form_action[$prefix.'form_action'];
								$this->execute_single_form_action($record, $ajax_handler, $current_action_name);
								
								/*if(isset($form_action[$prefix.'enable_conditions']) && $form_action[$prefix.'enable_conditions'] == 'yes' && isset($form_action[$prefix.'condition_action']) && isset($form_action[$prefix.'condition_chain'])){
									$condition_chain_id = $form_action[$prefix.'condition_chain'];
									
									// $module_instance = Module::instance();
									//$conditions = $module_instance->settings->get_elementor_condition_settings( get_the_ID() );
									
                  
									// $conditions = $this->get_elementor_condition_settings(\Elementor\Plugin::instance()->documents->get_current()->get_main_id());
									$conditions = $this->get_elementor_condition_settings(get_the_ID());
                  
									$condition = $this->get_condition_by_id($condition_chain_id, $conditions);
                  					// $form_field_setting = $this->get_form_field_data($condition, $field_with_settings);
									$check = $this->check_form_condition($condition, $record, $ajax_handler);

									if($check && $form_action[$prefix.'condition_action'] == 'execute'){
										$this->execute_single_form_action($record, $ajax_handler, $current_action_name);
									}
									else if(!$check && isset($form_action[$prefix.'next_action']) && $form_action[$prefix.'next_action'] == 'yes'){
										$ajax_handler->send();
									}
									else {
										continue;
									}
								}*/
							}
						}
					}

					$module = \ElementorPro\Modules\Forms\Module::instance();
					$activity_log = $module->get_component( 'activity_log' );
					if ( $activity_log ) {
						$activity_log->run( $record, $ajax_handler );
					}

					$cf7db = $module->get_component( 'cf7db' );
					if ( $cf7db ) {
						$cf7db->run( $record, $ajax_handler );
					}

					/**
					 * New Elementor form record.
					 *
					 * Fires before a new form record is send by ajax.
					 *
					 * @since 1.0.0
					 *
					 * @param Form_Record  $record An instance of the form record.
					 * @param Ajax_Handler $this   An instance of the ajax handler.
					 */
					do_action( 'elementor_pro/forms/new_record', $record, $ajax_handler );
					$ajax_handler->send();
				}

		}
	}

	public function execute_single_form_action($record, $ajax_handler, $current_action_name){

		$module = \ElementorPro\Modules\Forms\Module::instance();
		$actions = $module->get_form_actions();
		
		$submit_actions = $record->get_form_settings( 'submit_actions' );

		foreach ( $actions as $action ) {

			if ( ! in_array( $current_action_name, $submit_actions, true ) ) {
				continue;
			}

			if($current_action_name == $action->get_name()){
				$exception = null;

				try {
					$action->run( $record, $ajax_handler );
	
					// $ajax_handler->handle_bc_errors( $errors );
				} catch ( \Exception $e ) {
					$exception = $e;
	
					// Add an admin error.
					// if ( ! in_array( $exception->getMessage(), $ajax_handler->messages['admin_error'], true ) ) {
					// 	$ajax_handler->add_admin_error_message( "{$action->get_label()} {$exception->getMessage()}" );
					// }
	
					// Add a user error.
					$ajax_handler->add_error_message( $ajax_handler->get_default_message( self::ERROR, $ajax_handler->current_form['settings'] ) );
				}
	
				// $errors = array_merge( $ajax_handler->messages['error'], $ajax_handler->messages['admin_error'] );
	
				do_action( 'elementor_pro/forms/actions/after_run', $action, $exception );
			}
			
		}
	}

	public function get_form_field_data($condition, $form_fields = array()){
		
		$prefix = 'gloo_fluid_visibility_';

		if ( $condition[ $prefix . 'is_form_field' ] === 'yes' ) {
			if(is_array($form_fields) && count($form_fields) >= 1 && is_array($condition) && count($condition) >= 1){
				foreach($form_fields as $field){
					if($field['custom_id'] == $condition[$prefix.'condition_form_field_id']){
						return $field;
					}
				}
			}
		}

		return [];		
	}

	public function check_form_condition($condition, $record, $ajax_handler){
		
		if(is_array($condition) && count($condition) >= 1){
			
			$prefix = 'gloo_fluid_visibility_';

			// Get sumitetd Form data
			$raw_fields = $record->get( 'fields' );
			foreach ( $raw_fields as $id => $field ) {
				if($id == $condition[$prefix.'condition_form_field_id'] && $field['raw_value'] && $field['raw_value'] == $condition[$prefix.'condition_value']){
					return true;
					break;
				}		
				
			}
			
		}
		return false;
	}

	public function get_condition_by_id($id, $conditions = []){

		if($conditions && is_array($conditions) && count($conditions) >= 1){
			$prefix = 'gloo_fluid_visibility_';
			$condition_index    = array_search( $id, array_column( $conditions, '_id' ) );
			$condition          = $conditions[ $condition_index ];
			return $condition;
		}
		return [];
	}


  public function get_elementor_condition_settings( $id ) {

		// $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $id );
		
		// db($document->get_elements_data());
		// db(get_class_methods($document));
		// db($document->get_settings());
		// db($document->get_settings_for_display('gloo_fluid_visibility_conditions')[0]['gloo_fluid_visibility_condition_field']);
		
		
		// db($object);

		
		// db( $post->ID);
		
		// db( $post->ID);
		
		
		

		// $document = \Elementor\Plugin::instance()->documents->get( $id );
		// $document->refresh_post();
		// \Elementor\Plugin::$instance->db->switch_to_post( $post_object->ID );
		
		
		// db(get_class_methods($document));
		
		// db($document->get_settings_for_display( $this->prefix . 'conditions' )[0]['gloo_fluid_visibility_condition_field']);
    $prefix = 'gloo_fluid_visibility_';
		$document = \Elementor\Plugin::instance()->documents->get( $id );
		if ( ! empty( $document ) ) {
			if($document->get_name() == 'jet-listing-items'){
				$conditions = $document->get_settings()[$prefix . 'conditions'];
				if($conditions && is_array($conditions) && count($conditions) >= 1 ){
					foreach($conditions as $key=>$single_condition){
						if(isset($single_condition['__dynamic__']) && is_array($single_condition['__dynamic__']) && count($single_condition['__dynamic__']) >= 1){
							// global $post;
							$post_object = jet_engine()->listings->data->get_current_object();
							$main_listing_settings = $document->get_settings();

							// $post = get_post($post_object->ID, OBJECT );
							// setup_postdata( $post );

							$sub_listing = \Elementor\Plugin::instance()->documents->get( $post_object->ID );
							$sub_listing_settings = $sub_listing->get_settings();
							$sub_listing->set_settings( $main_listing_settings );

							$sub_listing_settings_frontend = $sub_listing->get_settings_for_display( $prefix . 'conditions' );
							$sub_listing_settings_frontend['current_listing_id'] = $post_object->ID;
							$sub_listing->set_settings( $sub_listing_settings );
							return $sub_listing_settings_frontend;
							// db($sub_listing->get_settings_for_display('gloo_fluid_visibility_conditions')[0]['gloo_fluid_visibility_condition_field']);
							// wp_reset_query();
							break;
						}
					}
				}
			}
			return $document->get_settings_for_display( $prefix . 'conditions' );
		}

		return false;
	}


} // BBWP_CustomFields class
