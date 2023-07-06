<?php
namespace Gloo\Modules\CheckoutAnything;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
 
class RepeaterField extends \ElementorPro\Modules\Forms\Fields\Field_Base {
 
    public $depended_scripts = [
        // 'tinymce-cdn',
    ];
 
    public function get_type() {
        return 'repeater_field';
    }
 
    public function get_name() {
        return __( 'Repeater Field', 'gloo_for_elementor' );
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

        $repeater_field_sources = ['' => __('--Select--', 'gloo_for_elementor')];

        if ( function_exists( 'jet_engine' ) ) {
            $repeater_field_sources['jet_engine'] = __( 'JetEngine', 'gloo_for_elementor' );
        }

        if ( function_exists( 'acf' ) ) {
            $repeater_field_sources['acf'] = __( 'ACF', 'gloo_for_elementor' );
        }

		$field_controls = [
			'gloo_repeater_field_type'   => [
				'name'         => 'gloo_repeater_field_type',
				'label'        => __( 'Repeater Field Type', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'condition'    => [
					'field_type' => $this->get_type(),
				],
				'default'      => '',
				'options'      => $repeater_field_sources,
				// 'description'  => 'Depth of terms in the hierarchy to show.',
				// 'tab'          => 'advanced',
                // 'inner_tab'    => 'form_fields_advanced_tab',
                'tab'          => 'content',
				// 'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
            'relationship_tag_meta_key' =>
			array(
                'name' => 'gloo_repeater_field_key',
				'label'     => __( 'Repeater Field Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'gloo_repeater_field_type' => ['jet_engine', 'acf']
				],
			)
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
 
        // $settings = $form->get_settings_for_display( 'form_fields' );
        // $depth       = $settings[ $item_index ]['gloo_test_dropdown'];

        $form->add_render_attribute( 'textarea' . $item_index, 'class', 'elementor-tinymce' );
 		
        // echo '<textarea ' . $form->get_render_attribute_string( 'input' . $item_index ) . '>';if(isset($item['field_value'])){ echo $item['field_value']; }echo '</textarea>';
		

        $field = acf_get_field('gloo_acf_repeater_field');
        $field = $this->load_field($field);
        if(!empty($field) && isset($field['type']) && $field['type'] == 'repeater' && isset($field['sub_fields']) && is_array($field['sub_fields']) && count($field['sub_fields']) >= 1){
            // acf_render_fields($field);
            // render_field($field);
            acf_render_field_wrap($field);
            // echo '<div class="gloo_repeater_field_container">';
            foreach($field['sub_fields'] as $key=>$sub_field){
                if($sub_field['type'] == 'text' || $sub_field['type'] == 'number'){
                    // acf_render_field_wrap( $sub_field );
                    // echo '<label>'.$sub_field['label'].'</label>';
                    // echo '<input type="'.$sub_field['type'].'" name="'.$sub_field['field_61e9332531bb0'].'[]" />';
                }
            }
            // echo '<p><a href="#" class="add-row">Add Row</a></p>';
            // echo '</div>';
        }

        
    }
 
    public function register_field_type( $fields ) {
        \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( self::get_type(), $this );
        $fields[ self::get_type() ] = self::get_name();
        return $fields;
    }
 
    public function sanitize_field( $value, $field ) {
        return wp_kses_post( $field['raw_value'] );
    }
 
    public function __construct() {
        parent::__construct();
        add_filter( 'elementor_pro/forms/field_types', [ $this, 'register_field_type' ] );


        if(function_exists('acf_get_url')){

            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            $suffix = '';
            $version = acf_get_setting('version');
		    $min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            $min = '';

            wp_register_script( 'acf', acf_get_url( 'assets/js/acf' . $suffix . '.js' ), array( 'jquery' ), $version );
            wp_register_script( 'acf-input', acf_get_url( 'assets/js/acf-input' . $suffix . '.js' ), array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-resizable', 'acf' ), $version );
            wp_register_script( 'acf-field-group', acf_get_url( 'assets/js/acf-field-group' . $suffix . '.js' ), array( 'acf-input' ), $version );

            // register scripts
            wp_register_script( 'acf-pro-input', acf_get_url( "pro/assets/js/acf-pro-input{$min}.js" ), array('acf-input'), $version );
            wp_register_script( 'acf-pro-field-group', acf_get_url( "pro/assets/js/acf-pro-field-group{$min}.js" ), array('acf-field-group'), $version );
            
            wp_enqueue_script('acf');
            wp_enqueue_script('acf-pro-input');
            wp_enqueue_script('acf-pro-field-group');

            // register styles
            wp_register_style( 'acf-global', acf_get_url( 'assets/css/acf-global.css' ), array(), $version );
            wp_register_style( 'acf-input', acf_get_url( 'assets/css/acf-input.css' ), array('acf-global'), $version );
            wp_register_style( 'acf-field-group', acf_get_url( 'assets/css/acf-field-group.css' ), array('acf-input'), $version );

            wp_register_style( 'acf-pro-input', acf_get_url( 'pro/assets/css/acf-pro-input.css' ), array('acf-input'), $version ); 
            wp_register_style( 'acf-pro-field-group', acf_get_url( 'pro/assets/css/acf-pro-field-group.css' ), array('acf-input'), $version );

            wp_enqueue_style('acf-field-group');
            wp_enqueue_style('acf-pro-input');
            wp_enqueue_style('acf-pro-field-group');
        }
        
    }





    /******************************** */
    public function load_field( $field ) {
		
		// min/max
		$field['min'] = (int) $field['min'];
		$field['max'] = (int) $field['max'];
		
		
		// vars
		$sub_fields = acf_get_fields( $field );
		
		
		// append
		if( $sub_fields ) {	
			$field['sub_fields'] = $sub_fields;
		}		
		
		// return
		return $field;
		
	}
}
