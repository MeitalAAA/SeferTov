<?php
namespace Gloo\RepeaterField;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
 
class RepeaterEndField extends \ElementorPro\Modules\Forms\Fields\Field_Base {
 
    public $depended_scripts = [
        'gloo_repeater_field_js',
    ];
 
    public function get_type() {
        return 'gloo_repeater_end_field';
    }
 
    public function get_name() {
        return __( 'Repeater End Field', 'gloo_for_elementor' );
    }
 

    /**
	 * @param Widget_Base $widget
	 */
	public function update_controls( $widget ) {
	}

    /**
     * @param      $item
     * @param      $item_index
     * @param Form $form
     */
    public function render( $item, $item_index, $form ) {
        
    }
 
    public function register_field_type( $fields ) {
        \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( self::get_type(), $this );
        $fields[ self::get_type() ] = self::get_name();
        return $fields;
    }

 
    public function __construct() {
        parent::__construct();
        add_filter( 'elementor_pro/forms/field_types', [ $this, 'register_field_type' ] );
        $script_abs_path = gloo()->modules_path( 'repeater-field/assets/js/script.js');
        wp_register_script( 'gloo_repeater_field_js', gloo()->plugin_url( 'includes/modules/repeater-field/assets/js/script.js'), array('jquery'), get_file_time($script_abs_path));
    }

}
