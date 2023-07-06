<?php

namespace Gloo\Modules\Custom_Webhook;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use Gloo\Modules\Interactor\Module as Interactor;

class Settings {

	private $prefix = 'gloo_ct_connector_';

	public function __construct() {
		add_action( 'elementor/element/before_section_end', [ $this, 'add_settings' ], 10, 2 );
		add_action( 'gloo/modules/interactor/trigger_loop_item', [ $this, 'check_custom_webhook_settings' ] );
	}
 
	public function check_custom_webhook_settings( $trigger ) {

		if ( ! isset( $trigger[ $this->prefix . 'triggers' ] ) || ! $trigger[ $this->prefix . 'triggers' ] ) {
			return;
		}

		$custom_items = $trigger[ $this->prefix . 'triggers' ];

		foreach ( $custom_items as $custom_item ) {
			$code = $this->generate_js_code( $custom_item );
			add_filter( "gloo/modules/interactor/trigger_loop_item/trigger_functions/{$trigger['_id']}", function () use ( $code ) {
				return $code;
			} );
		}
	}

	public function generate_js_code( $custom_item ) {

		$interactor_settings       = Interactor::instance()->settings;
		$current_document_settings = $interactor_settings->get_current_document()->get_settings_for_display();

		if ( ! isset( $current_document_settings[ $this->prefix ] ) ) {
			return;
		}
		$custom_items = $current_document_settings[ $this->prefix ];
		$variables       = $current_document_settings['gloo_interactor_variables'];

		$custom_js = '';
		foreach ( $custom_items as $custom_item ) {

			$variable_output = [];
			foreach ( $custom_item[ $this->prefix . 'interactor_variables' ] as $custom_variable ) {
				$var_key = array_search( $custom_variable, array_column( $variables, '_id' ) );
				if ( $var_key === false ) {
					continue;
				}

				$current_var = $variables[ $var_key ];
				if ( ! $current_var['gloo_interactor_variable_name'] || ! $current_var['gloo_interactor_variable_value'] ) {
					continue;
				}
				$variable_output[ $current_var['gloo_interactor_variable_name'] ] = $current_var['gloo_interactor_variable_value'];
			}

			if ( ! $variable_output ) {
				continue;
			}

			$webhook_url = $custom_item[$this->prefix .'webhook_url'];
			$type = $custom_item[$this->prefix .'type'];
			$custom_request = $webhook_url . "?" . http_build_query($variable_output);
			
			$custom_js .= "var settings = {
			 	'url': '{$custom_request}',
				'method': '{$type}'
			};
			$.ajax(settings).fail(function(jqXHR, textStatus, errorThrown) { console.log(errorThrown);});";
		}

		return $custom_js;
	}

	public function add_settings( $element, $section_id ) {

		if ( $section_id !== 'gloo_interactor_' ) {
			return;
		}

		$interactor_settings = Interactor::instance()->settings;

		// add data layer to interactor triggers
		$interactor_triggers = $element->get_controls( 'gloo_interactor_triggers' );

		$interactor_triggers['fields'][ $this->prefix . 'triggers' ] = [
			'label'    => __( 'Custom Webhook', 'gloo_for_elementor' ),
			'type'     => \Elementor\Controls_Manager::SELECT2,
			'options'  => $interactor_settings->get_settings_as_options( $this->prefix, $this->prefix . 'title' ),
			'name'     => $this->prefix . 'triggers',
			'multiple' => true,
		];
		$element->update_control( 'gloo_interactor_triggers', $interactor_triggers );

		$custom_repeater = new Repeater();

		$custom_repeater->add_control(
			$this->prefix . 'title',
			[
				'label'       => __( 'Webhook Name', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Name', 'gloo_for_elementor' ),
			]
		);

		$custom_repeater->add_control(
			$this->prefix . 'webhook_url',
			[
				'label'  => __( 'Webhook Url', 'gloo_for_elementor' ),
				'type'   => Controls_Manager::TEXT,
			]
		);

		$custom_repeater->add_control(
			$this->prefix . 'type',
			[
				'label'   => __( 'Method', 'gloo_for_elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post'   => 'Post',
					'get' => 'Get',
					'put' => 'Put',
					'delete' => 'Delete',
					'patch' => 'Patch'
				],
			]
		);
		
		$custom_repeater->add_control(
			$this->prefix . 'interactor_variables',
			[
				'type'     => Controls_Manager::SELECT2,
				'label'    => __( 'Variables', 'gloo_for_elementor' ),
				'options'  => $interactor_settings->get_variables(),
				'multiple' => true,
			]
		);

		$element->add_control(
			$this->prefix,
			[
				'label'         => __( 'Custom Webhook', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $custom_repeater->get_controls(),
				'title_field'   => '{{{ ' . $this->prefix . 'title }}}',
			]
		);
	}
}
