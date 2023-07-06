<?php

namespace Gloo\Modules\Dynamic_Attributes;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use Elementor\Element_Base;

class Settings {
	private $prefix = 'gloo_da_';

	public function __construct() {
		add_action( 'elementor/element/before_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/element/after_add_attributes', [ $this, 'render_attributes' ] );

	}

	/**
	 * @param $element Element_Base
	 */

	public function render_attributes( Element_Base $element ) {
		$settings = $element->get_settings_for_display();

		if ( isset( $settings[ $this->prefix . 'repeater' ] ) && ! empty( $settings[ $this->prefix . 'repeater' ] ) ) {

			$dynamic_attributes = [];
			$repeater           = $settings[ $this->prefix . 'repeater' ];
			foreach ( $repeater as $item ) {
				if ( empty( $item[ $this->prefix . 'key' ] ) ) {
					continue;
				}
				$value                = $item[ $this->prefix . 'value' ] ? $item[ $this->prefix . 'key' ] . '|' . $item[ $this->prefix . 'value' ] : $item[ $this->prefix . 'key' ];
				$dynamic_attributes[] = $value;
			}

			if ( ! $dynamic_attributes ) {
				return;
			}

			$attributes_string  = implode( ',', $dynamic_attributes );
			$dynamic_attributes = \Elementor\Utils::parse_custom_attributes( $attributes_string, ',' );
			$black_list         = $this->get_black_list_attributes();

			if ( ! $dynamic_attributes ) {
				return;
			}

			foreach ( $dynamic_attributes as $attribute => $value ) {
				if ( ! in_array( $attribute, $black_list, true ) ) {
					$element->add_render_attribute( '_wrapper', $attribute, $value );
				}
			}
		}
	}

	private function get_black_list_attributes() {

		$black_list = [
			'id',
			'class',
			'data-id',
			'data-settings',
			'data-element_type',
			'data-widget_type',
			'data-model-cid'
		];

		$black_list = apply_filters( 'gloo/modules/dynamic_attributes/black_list', $black_list );

		return $black_list;
	}

	public function register_controls( $element, $section_id ) {


		if ( ! $element instanceof Element_Base ) {
			return;
		}
		if ( $section_id !== '_section_attributes' ) {
			return;
		}

		$element->add_control(
			$this->prefix . 'heading',
			[
				'label'     => __( 'Dynamic Attributes', 'gloo_for_elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$da_repeater = new Repeater();

		$da_repeater->add_control(
			$this->prefix . 'name',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Name', 'gloo_for_elementor' ),
				'placeholder' => __( 'Optional', 'gloo_for_elementor' ),
			)
		);

		$da_repeater->add_control(
			$this->prefix . 'key',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Key', 'gloo_for_elementor' ),
				'placeholder' => __( 'Key', 'gloo_for_elementor' ),
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						DynamicTags::BASE_GROUP,
						DynamicTags::TEXT_CATEGORY,
						DynamicTags::URL_CATEGORY,
						DynamicTags::GALLERY_CATEGORY,
						DynamicTags::IMAGE_CATEGORY,
						DynamicTags::MEDIA_CATEGORY,
						DynamicTags::POST_META_CATEGORY,
						DynamicTags::NUMBER_CATEGORY,
						DynamicTags::COLOR_CATEGORY,
					),
				),
			)
		);

		$da_repeater->add_control(
			$this->prefix . 'value',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Value', 'gloo_for_elementor' ),
				'placeholder' => __( 'Value', 'gloo_for_elementor' ),
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						DynamicTags::BASE_GROUP,
						DynamicTags::TEXT_CATEGORY,
						DynamicTags::URL_CATEGORY,
						DynamicTags::GALLERY_CATEGORY,
						DynamicTags::IMAGE_CATEGORY,
						DynamicTags::MEDIA_CATEGORY,
						DynamicTags::POST_META_CATEGORY,
						DynamicTags::NUMBER_CATEGORY,
						DynamicTags::COLOR_CATEGORY,
					),
				),
			)
		);


		$element->add_control(
			$this->prefix . 'repeater',
			[
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $da_repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'name}}}',
				'label_block'   => false,

			]
		);

	}


}
