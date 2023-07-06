<?php

namespace Gloo\Modules\DataLayer_Connector;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use Gloo\Modules\Interactor\Module as Interactor;

class Settings {

	private $prefix = 'gloo_dl_connector_';

	public function __construct() {
		add_action( 'elementor/element/before_section_end', [ $this, 'add_settings' ], 10, 2 );
		add_action( 'gloo/modules/interactor/trigger_loop_item', [ $this, 'check_datalayer_settings' ] );
		add_action( 'wp_head', [ $this, 'datalayer_head_html' ], 1, 0 );
		add_action( 'wp_body_open', [ $this, 'datalayer_body_html' ], 1, 0 );
		add_action( 'wp_head', [ $this, 'enqueue_gtm' ], 2, 0 );


	}

	public function datalayer_body_html() {

		$key = $this->get_gtm_key();
		if ( ! $key ) {
			return;
		}

		echo '
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $key . '"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
';
	}

	public function get_gtm_key() {
		if ( ! get_option( 'datalayer_connector_js' ) || ! get_option( 'datalayer_connector_js' ) ) {
			return;
		}

		return get_option( 'datalayer_connector_key' );
	}

	public function enqueue_gtm() {
		$key = $this->get_gtm_key();
		if ( ! $key ) {
			return;
		}

		echo "
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$key}');</script>
<!-- End Google Tag Manager -->
";
	}

	public function datalayer_head_html() {

		$value = apply_filters( 'gloo/modules/interactor/datalayer_default_value', '' );

		echo "
<script data-cfasync='false' data-pagespeed-no-defer type='text/javascript'>dataLayer = [$value];</script>
";
	}

	public function check_datalayer_settings( $trigger ) {

		if ( ! isset( $trigger[ $this->prefix . 'triggers' ] ) || ! $trigger[ $this->prefix . 'triggers' ] ) {
			return;
		}

		$datalayer_items = $trigger[ $this->prefix . 'triggers' ];

		foreach ( $datalayer_items as $datalayer_item ) {
			$code = $this->generate_js_code( $datalayer_item );
			add_filter( "gloo/modules/interactor/trigger_loop_item/trigger_functions/{$trigger['_id']}", function () use ( $code ) {
				return $code;
			} );
		}
	}

	public function generate_js_code( $current_datalayer_item ) {

		$interactor_settings       = Interactor::instance()->settings;
		$current_document_settings = $interactor_settings->get_current_document()->get_settings_for_display();

		if ( ! isset( $current_document_settings[ $this->prefix ] ) || ! $current_datalayer_item ) {
			return;
		}

		$datalayer_items = $current_document_settings[ $this->prefix ]; // all dataLayers
		$variables       = $current_document_settings['gloo_interactor_variables']; // all variables

		$current_datalayer_key = array_search( $current_datalayer_item, array_column( $datalayer_items, '_id' ) );

		if ( $current_datalayer_key === false || ! isset( $datalayer_items[ $current_datalayer_key ] ) ) { // not found
			return;
		}

		$datalayer_item  = $datalayer_items[ $current_datalayer_key ];
		$datalayer_js    = '';
		$variable_output = [];

		foreach ( $datalayer_item[ $this->prefix . 'interactor_variables' ] as $datalayer_variable ) {
			$var_key = array_search( $datalayer_variable, array_column( $variables, '_id' ) );
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
			return;
		}

		$variable_output       = json_encode( $variable_output );
		$datalayer_object_name = $datalayer_item[ $this->prefix . 'object_name' ] ? trim( $datalayer_item[ $this->prefix . 'object_name' ] ) : 'dataLayer';
		$datalayer_js          .= "{$datalayer_object_name}.push({$variable_output});";

		return $datalayer_js;
	}

	public function add_settings( $element, $section_id ) {

		if ( $section_id !== 'gloo_interactor_' ) {
			return;
		}

		$interactor_settings = Interactor::instance()->settings;

		// add data layer to interactor triggers
		$interactor_triggers = $element->get_controls( 'gloo_interactor_triggers' );

		$interactor_triggers['fields'][ $this->prefix . 'triggers' ] = [
			'label'    => __( 'DataLayer to fire', 'gloo_for_elementor' ),
			'type'     => \Elementor\Controls_Manager::SELECT2,
			'options'  => $interactor_settings->get_settings_as_options( $this->prefix, $this->prefix . 'title' ),
			'name'     => $this->prefix . 'triggers',
			'multiple' => true,
		];
		$element->update_control( 'gloo_interactor_triggers', $interactor_triggers );

		$datalayer_repeater = new Repeater();

		$datalayer_repeater->add_control(
			$this->prefix . 'title',
			[
				'label'       => __( 'Title', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Title', 'gloo_for_elementor' ),
			]
		);


		$datalayer_repeater->add_control(
			$this->prefix . 'interactor_variables',
			[
				'type'     => Controls_Manager::SELECT2,
				'label'    => __( 'Variables', 'gloo_for_elementor' ),
				'options'  => $interactor_settings->get_variables(),
				'multiple' => true,
			]
		);

		$datalayer_repeater->add_control(
			$this->prefix . 'object_name',
			[
				'label'       => __( 'DataLayer Object', 'gloo_for_elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( '(Advanced) Leave empty to use the default dataLayer object.', 'gloo_interactor' ),
				'placeholder' => __( 'dataLayer', 'gloo_interactor' ),
			]
		);

		$element->add_control(
			$this->prefix,
			[
				'label'         => __( 'Data Layer', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $datalayer_repeater->get_controls(),
				'title_field'   => '{{{ ' . $this->prefix . 'title }}}',

			]
		);

	}


}