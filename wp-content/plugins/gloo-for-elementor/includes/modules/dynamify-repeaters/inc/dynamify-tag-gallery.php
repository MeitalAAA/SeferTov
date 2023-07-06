<?php

namespace Gloo\Modules\Dynamify_Repeaters;

class Dynamify_Tag_Gallery extends Dynamify_Tag {

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
		return 'gloo-dynamify-tag-gallery';
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
			\Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY,
		];
	}


	/**
	 * Render
	 *
	 * Prints out the value of the Dynamic tag
	 *
	 * @return array
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

		$result = [];
		switch ( $source ) {
			case 'acf':
				$array = $this->acf_get_data( $field, $subfield, $index, $context );
				if ( $array && isset( $array[0] ) ) {
					switch ( gettype( $array[0] ) ) {
						case 'object':
						case 'array':
							foreach ( $array as $item ) {
								$result[] = [
									'id'  => $item['id'],
									'url' => $item['url'],
								];
							}

							break;
						case 'string':
							foreach ( $array as $item ) {
								$result[] = [
									'id'  => attachment_url_to_postid( $item ),
									'url' => $item,
								];
							}
							break;
						case 'integer':
							foreach ( $array as $item ) {
								$src = wp_get_attachment_image_src( $item );

								if ( $src ) {
									$src = $src[0];
								}
								$result[] = [
									'id'  => $item,
									'url' => $src,
								];
							}
							break;
					}
				}
				break;
			case 'jet_engine':
				$ids = $this->jet_engine_get_data( $field, $subfield, $index, $context );

				if ( $ids && ! is_array( $ids ) ) {
					$ids = explode( ',', $ids );
				}

				if ( $ids ) {
					foreach ( $ids as $id ) {
						$src = wp_get_attachment_image_src( $id );
						if ( $src ) {
							$src = $src[0];
						}
						$result[] = [
							'id'  => $id,
							'url' => $src,
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