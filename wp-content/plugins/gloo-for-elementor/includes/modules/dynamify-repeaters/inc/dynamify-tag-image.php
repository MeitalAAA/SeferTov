<?php

namespace Gloo\Modules\Dynamify_Repeaters;

class Dynamify_Tag_Image extends Dynamify_Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'gloo-dynamify-tag-image';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @return array
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_categories() {
		return [
			\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
		];
	}


	/**
	 * Render
	 *
	 * Prints out the value of the Dynamic tag
	 *
	 * @return array|string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_value( array $options = array() ) {

		$source         = $this->get_settings_for_display( 'source' );
		$context        = $this->get_settings_for_display( 'context' );
		$field          = $this->get_settings_for_display( 'field' );
		$subfield       = $this->get_settings_for_display( 'subfield' );
		$index          = intval( $this->get_settings_for_display( 'index' ) );


		if ( $source === 'jet_engine' && $context === 'options_page' ) {
			$field = $this->get_settings_for_display( 'jet_options_page_field' );
		}

		$result = '';
		switch ( $source ) {
			case 'acf':
				$mixed = $this->acf_get_data( $field, $subfield, $index, $context );
				if ( $mixed ) {
					switch ( gettype( $mixed ) ) {
						case 'object':
						case 'array':
							$result = [
								'id'  => $mixed['id'],
								'url' => $mixed['url'],
							];
							break;
						case 'string':
							$result = [
								'id'  => attachment_url_to_postid($mixed),
								'url' => $mixed,
							];
							break;
						case 'integer':
							$src = wp_get_attachment_image_src( $mixed );
							if ( $src ) {
								$result = [
									'id'  => $mixed,
									'url' => $src[0],
								];
							}

							break;
					}
				}
				break;
			case 'jet_engine':
				$id = $this->jet_engine_get_data( $field, $subfield, $index, $context );
				if ( $id && is_numeric( $id ) ) {
					$src = wp_get_attachment_image_src( $id );
					if ( $src ) {
						$result = [
							'id'  => $id,
							'url' => $src[0],
						];
					}
				}
				break;
		}

		if ( empty( $result ) && $this->get_settings( 'fallback' ) ) {
			$result = $this->get_settings( 'fallback' );
		}

		return $result;
	}
}