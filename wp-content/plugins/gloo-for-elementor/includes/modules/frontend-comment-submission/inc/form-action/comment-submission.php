<?php
namespace Gloo\Modules\Form_Comment_Submission;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Frontend_Comment_Submission extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	private $prefix = 'cc_comment';
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'frontend_comment_submission';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return __( 'Submit Comment Form Action', 'gloo_for_elementor' );
	}

	/**
	 * Run
	 *
	 * Runs the action after submit
	 *
	 * @access public
	 *
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {

		$settings = $record->get( 'form_settings' );
		$post_field_id   = $settings[ $this->prefix . 'post_id' ] ;
		$comment_field_id   = $settings[ $this->prefix . 'comment_text' ] ;
		// Get submitted form data
		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		if((isset($fields[$post_field_id]) && !empty($fields[$post_field_id])) && (isset($fields[$comment_field_id]) && !empty($fields[$comment_field_id]))) {
			$current_user = wp_get_current_user();
 
			$data = [
				'comment_post_ID'      => $fields[$post_field_id],
				'comment_author'       => $current_user->data->user_login,
				'comment_content'      => $fields[$comment_field_id],
				'comment_type'         => 'comment',
				'user_id'              => $current_user->ID,
				'comment_approved'     => 1,
				'comment_author_email' => $current_user->data->user_email,
				'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
			];
			
			wp_insert_comment( $data);
		}
 
	}
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			$this->prefix . 'section',
			[
				'label' => __( 'Submit Comment Form Action', 'gloo' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

    	$widget->add_control(
			$this->prefix.'post_id',
			[
				'label' => __( 'Post Field ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '',
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter your form field id which consist queried post id.', 'gloo' ),
			]
		);

		$widget->add_control(
			$this->prefix.'comment_text',
			[
				'label' => __( 'Comment Text Field ID', 'gloo' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '',
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter your form field id which consist comment text.', 'gloo' ),
			]
		);


		$widget->add_control(
			$this->prefix.'description',
			[
				'raw' => __( 'Post Field ID and Comment Text Field ID are required.', 'gloo' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
        		'content_classes' => 'elementor-descriptor',
			]
		);
		
		$widget->end_controls_section();
	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 *
	 * @param array $element
	 */
	public function on_export( $element ) {
	}
}