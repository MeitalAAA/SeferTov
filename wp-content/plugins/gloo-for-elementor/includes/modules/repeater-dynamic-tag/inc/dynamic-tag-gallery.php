<?php

namespace Gloo\Modules\Repeater_Dynamic_Tag;

class Repeater_Dynamic_Tag_Gallery extends Repeater_Dynamic_Tag {

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
		return 'gloo-repeater-tag-gallery';
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
	 * @return void
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_value( array $options = array() ) {

		$source         = $this->get_settings_for_display( 'source' );
		$context        = $this->get_settings_for_display( 'context' );
		$field          = $this->get_settings_for_display( 'field' );
		$subfield       = $this->get_settings_for_display( 'subfield' );
		$is_follow_mode = $this->get_settings_for_display( 'follow_mode' ) === 'yes';
		$index          = intval( $this->get_settings_for_display( 'index' ) );


		if ( $source === 'jet_engine' && $context === 'options_page' ) {
			$field = $this->get_settings_for_display( 'jet_options_page_field' );
		}

		if ( $is_follow_mode ) {
			$index = jet_engine()->listings->data->get_index();

			// repeater already selected for the listing
			$is_listing_source_repeater = $this->is_listing_source_repeater();

			if ( $is_listing_source_repeater ) {
				$repeater_data = $this->get_listing_repeater();

				if ( $repeater_data['repeater_source'] === 'jet_engine_options' ) {
					$repeater_data['repeater_source'] = 'jet_engine';
					$context                          = 'options_page';
				}
				$source = $repeater_data['repeater_source'];
				$field  = $repeater_data['repeater_field'];
			}
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

		return $result;
	}
}