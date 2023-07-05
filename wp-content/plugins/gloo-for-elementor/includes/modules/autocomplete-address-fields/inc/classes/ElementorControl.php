<?php
namespace ByteBunch\FluidDynamics;

class ElementorControl extends \Elementor\Base_Data_Control {

	public function get_type() {
		return 'emojionearea';
	}


	public function enqueue() {
		// Styles
		wp_register_style( 'emojionearea', 'https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.1/emojionearea.css', [], '3.4.1' );
		wp_enqueue_style( 'emojionearea' );

		// Scripts
		wp_register_script( 'emojionearea', 'https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.1/emojionearea.js', [], '3.4.1' );
		wp_register_script( 'emojionearea-control', gloo()->modules_path( 'autocomplete-address-fields/js/emojionearea-control.js'), [ 'emojionearea', 'jquery' ], '1.0.0' );
		wp_enqueue_script( 'emojionearea-control' );

	}



	protected function get_default_settings() {
		return [
			'label_block' => true,
			'rows' => 3,
			'emojionearea_options' => [],
		];
	}



	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<textarea id="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-tag-area" rows="{{ data.rows }}" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}"></textarea>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}