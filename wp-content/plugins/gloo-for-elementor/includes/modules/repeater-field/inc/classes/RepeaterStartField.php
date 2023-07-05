<?php
namespace Gloo\RepeaterField;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
 
class RepeaterStartField extends \ElementorPro\Modules\Forms\Fields\Field_Base {
 
    public $field_settings = array();
    public $depended_scripts = [
        // 'tinymce-cdn',
    ];
 
    public function get_type() {
        return 'gloo_repeater_start_field';
    }
 
    public function get_name() {
        return __( 'Repeater Start Field', 'gloo_for_elementor' );
    }
 

    /**
	 * @param Widget_Base $widget
	 */
	public function update_controls( $widget ) {

        $elementor = \Elementor\Plugin::instance();
        $control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

        if ( is_wp_error( $control_data ) ) {
			return;
		}

        $repeater_return_type = [
            'default' => __('Default', 'gloo_for_elementor'),
            'array' => __('Array', 'gloo_for_elementor'),
            'string' => __('String', 'gloo_for_elementor'),
        ];

        $field_controls = [
			'gloo_repeater_return_type'   => [
				'name'         => 'gloo_repeater_return_type',
				'label'        => __( 'Repeater Field Return Type', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
            'array' => __('Array', 'gloo_for_elementor'),
				'default'      => 'default',
				'options'      => $repeater_return_type,
				// 'description'  => 'Depth of terms in the hierarchy to show.',
				'tab'          => 'advanced',
                'inner_tab'    => 'form_fields_advanced_tab',
                // 'tab'          => 'content',
				// 'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
            'gloo_repeater_return_type_delimiter'   => [
				'name'         => 'gloo_repeater_return_type_delimiter',
				'label'        => __( 'Repeater Field Delimiter', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'condition'    => [
					'gloo_repeater_return_type' => 'string',
                    'field_type' => $this->get_type(),
				],
                'default'      => ', ',
				// 'description'  => 'Depth of terms in the hierarchy to show.',
				'tab'          => 'advanced',
                'inner_tab'    => 'form_fields_advanced_tab',
                // 'tab'          => 'content',
				// 'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
            /*'gloo_repeater_return_type_length'   => [
				'name'         => 'gloo_repeater_return_type_length',
				'label'        => __( 'Repeater Field Length', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::NUMBER,
				'condition'    => [
					'gloo_repeater_return_type' => 'array',
                    'field_type' => $this->get_type(),
				],
                'default'      => '1',
				// 'description'  => 'Depth of terms in the hierarchy to show.',
				'tab'          => 'advanced',
                'inner_tab'    => 'form_fields_advanced_tab',
                // 'tab'          => 'content',
				// 'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],*/
		];

        $control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}

    /**
     * @param      $item
     * @param      $item_index
     * @param Form $form
     */
    public function render( $item, $item_index, $form ) {
        
    }

    public function repeater_start_field_validation($field, $record, $ajax_handler){

        $fields = $record->get_form_settings('form_fields');
        // $fields = $record->get( 'fields' );
        if(!empty($fields)){

            $start_repeater = false;
            
            foreach($fields as $single_field){

                $field_reset = false;
                $field_id = $single_field['custom_id'];
                
                if($start_repeater && isset($single_field['custom_id']) && !empty($this->field_settings)){
                    
                    $start_repeater_id = $this->field_settings['custom_id'];
                    
                    if(isset($_POST['form_fields']) && isset($_POST[$start_repeater_id]) && is_array($_POST[$start_repeater_id]) && count($_POST[$start_repeater_id]) >= 1){
                        
                        if(!isset($_POST['gloo_repeater_fields'][$field_id]))
                            $_POST['gloo_repeater_fields'][$field_id] = $_POST['form_fields'][$field_id];

                        if(isset($this->field_settings['gloo_repeater_return_type'])){
                            if($this->field_settings['gloo_repeater_return_type'] == 'string' && isset($this->field_settings['gloo_repeater_return_type_delimiter']) && !empty($this->field_settings['gloo_repeater_return_type_delimiter'])){
                                $string_value = '';
                                if(!empty($_POST['form_fields'][$field_id]) && is_array($_POST['form_fields'][$field_id]) && isset($_POST['form_fields'][$field_id][0]) && !empty($_POST['form_fields'][$field_id][0])){
                                    $string_value = $this->recursive_implode_array($_POST['form_fields'][$field_id], $this->field_settings['gloo_repeater_return_type_delimiter']);
                                }
                                $_REQUEST['form_fields'][$field_id] =  $string_value;
                                $_POST['form_fields'][$field_id] = $string_value;
                                $field_reset = true;
                            }else if($this->field_settings['gloo_repeater_return_type'] == 'array'){
                                $field_reset = true;
                            }
                        }

                        if($field_reset == false){
                            if(isset($_POST['form_fields'][$field_id]) && isset($_POST['form_fields'][$field_id][0])){
                                $_REQUEST['form_fields'][$field_id] = $_POST['form_fields'][$field_id][0];
                                $_POST['form_fields'][$field_id] = $_POST['form_fields'][$field_id][0];
                            }else{
                                $_REQUEST['form_fields'][$field_id] = '';
                                $_POST['form_fields'][$field_id] = '';
                            }
                        }
                        
                        
                        // db($field_id);db($_POST['form_fields'][$field_id]);db('end');
                    }
                }


                if($field['type'] == $single_field['field_type']){
                    $this->field_settings = $single_field;
                    $start_repeater = true;
                }
                if($single_field['field_type'] == 'gloo_repeater_end_field'){
                    $start_repeater = false;
                    $this->field_settings = array();
                }
                
            }
            // exit();
        }
        // db($_POST['form_fields']['time']);
        // $allow_resubscribe = $record->get_form_settings( 'allow_resubscribe' );
        // db($this->field_settings);
        // db(get_class_methods($record));
        // db($record);exit();
    }

    public function recursive_implode_array($array, $delimeter){
        $output = '';

        if(is_array($array) && count($array) >= 1){
        $counter = 0;
            foreach($array as $array_value){
                $counter++;
                if(is_array($array_value) && count($array_value) >= 1){
                    $output .= $this->recursive_implode_array($array_value, $delimeter);
                }elseif(is_string($array_value)){
                    if($counter == count($array))
                        $output .= $array_value;
                    else
                        $output .= $array_value.$delimeter;
                }
                
            }
        }
        return $output;
    }
 
    public function register_field_type( $fields ) {
        \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( self::get_type(), $this );
        $fields[ self::get_type() ] = self::get_name();
        return $fields;
    }

 
    public function __construct() {
        parent::__construct();
        add_filter( 'elementor_pro/forms/field_types', [ $this, 'register_field_type' ] );

        add_action( "elementor_pro/forms/validation/".self::get_type(), [$this, 'repeater_start_field_validation'], 2, 3);
    }

}
