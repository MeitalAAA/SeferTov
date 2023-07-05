<?php

namespace Gloo\Modules\Zapier_Connector;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use Gloo\Modules\Interactor\Module as Interactor;

class Settings {

	private $prefix = 'gloo_zp_connector_';

	public function __construct() {
		add_action( 'elementor/element/before_section_end', [ $this, 'add_settings' ], 10, 2 );
		add_action( 'gloo/modules/interactor/trigger_loop_item', [ $this, 'check_datalayer_settings' ] );
	}
 
	public function check_datalayer_settings( $trigger ) {

		if ( ! isset( $trigger[ $this->prefix . 'triggers' ] ) || ! $trigger[ $this->prefix . 'triggers' ] ) {
			return;
		}

		$zapier_items = $trigger[ $this->prefix . 'triggers' ];

		foreach ( $zapier_items as $zapier_item ) {
			$code = $this->generate_js_code( $zapier_item );
			add_filter( "gloo/modules/interactor/trigger_loop_item/trigger_functions/{$trigger['_id']}", function () use ( $code ) {
				return $code;
			} );
		}
	}

	public function generate_js_code( $zapier_item ) {

		$interactor_settings       = Interactor::instance()->settings;
		$current_document_settings = $interactor_settings->get_current_document()->get_settings_for_display();

		if ( ! isset( $current_document_settings[ $this->prefix ] ) ) {
			return;
		}
		$zapier_items = $current_document_settings[ $this->prefix ];
		$variables       = $current_document_settings['gloo_interactor_variables'];

		$zapier_js = '';
		foreach ( $zapier_items as $zapier_item ) {

			$variable_output = [];
			foreach ( $zapier_item[ $this->prefix . 'interactor_variables' ] as $zapier_variable ) {
				$var_key = array_search( $zapier_variable, array_column( $variables, '_id' ) );
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

			$webhook_url = $zapier_item['gloo_zp_connector_webhook_url'];
			$type = $zapier_item['gloo_zp_connector_type'];
			$zapier_request = $webhook_url . "?" . http_build_query($variable_output);
			
			$zapier_js .= "var settings = {
			 	'url': '{$zapier_request}',
				'method': '{$type}'
			};
			$.ajax(settings).done(function (response) {});";
		}

		return $zapier_js;
	}

	public function add_settings( $element, $section_id ) {

		if ( $section_id !== 'gloo_interactor_' ) {
			return;
		}

		$interactor_settings = Interactor::instance()->settings;

		// add data layer to interactor triggers
		$interactor_triggers = $element->get_controls( 'gloo_interactor_triggers' );

		$interactor_triggers['fields'][ $this->prefix . 'triggers' ] = [
			'label'    => __( 'Zapier Hook', 'gloo' ),
			'type'     => \Elementor\Controls_Manager::SELECT2,
			'options'  => $interactor_settings->get_settings_as_options( $this->prefix, $this->prefix . 'title' ),
			'name'     => $this->prefix . 'triggers',
			'multiple' => true,
		];
		$element->update_control( 'gloo_interactor_triggers', $interactor_triggers );

		$zapier_repeater = new Repeater();

		$zapier_repeater->add_control(
			$this->prefix . 'title',
			[
				'label'       => __( 'Webhook Name', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Name', 'gloo_for_elementor' ),
			]
		);

		$zapier_repeater->add_control(
			$this->prefix . 'webhook_url',
			[
				'label'  => __( 'Webhook Url', 'gloo_for_elementor' ),
				'type'   => Controls_Manager::TEXT,
			]
		);

		$zapier_repeater->add_control(
			$this->prefix . 'type',
			[
				'label'   => __( 'Method', 'gloo_for_elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post'   => 'Post',
					'get' => 'Get'
				],
			]
		);
		
		$zapier_repeater->add_control(
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
				'label'         => __( 'Zapier Webhook', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $zapier_repeater->get_controls(),
				'title_field'   => '{{{ ' . $this->prefix . 'title }}}',
			]
		);
	}
}
