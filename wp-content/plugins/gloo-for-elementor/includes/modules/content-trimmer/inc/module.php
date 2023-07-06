<?php

namespace Gloo\Modules\Content_Trimmer;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo-content-trimmer';

	public static $allowedWidgets = array();

	

	/**
	 * Returns the instance.
	 *
	 * @return Module
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {
		
		$allowedWidgets = array(
			'heading' => 'title',
			'text-editor' => 'editor',
			'theme-post-title' => 'title',
			'theme-post-excerpt' => 'excerpt',
			'theme-post-content' => 'unknown',
			'woocommerce-product-short-description' => 'unknown',
			'woocommerce-product-title' => 'title',
			//'flip-box' => 'description_text_a',
			//'post-title' => 'title',
			//'post-excerpt' => 'excerpt',
		);
		self::$allowedWidgets = apply_filters('otw/content-trimmer/allowed-widgets', $allowedWidgets);
		add_action( 'elementor/init', [ $this, 'init' ] );
	}

	public function init() {

		//$this->i18n();
		
		// Add Plugin actions

		add_action( 'elementor/element/before_section_end', [ $this, 'otw_elementor_widgets_modifier'], 10, 2);			
		
		add_filter( 'elementor/widget/render_content', [ $this, 'otw_render_widget_output' ], 1, 2);
		//add_action( 'elementor/widget/before_render_content', [ $this, 'otw_render_widget_output' ], 10, 1);
		add_action( 'elementor/frontend/before_render', [ $this, 'before_render_content' ], 10, 1);
		//add_action( 'elementor/frontend/heading/before_render', [ $this, 'before_render_content' ], 10, 1);
		
		//add_action( 'elementor/frontend/after_render', [ $this, 'otw_render_widget_output' ], 10, 2);
		//add_action( 'elementor/widget/heading/skins_init', [ $this, 'before_render_content' ], 10, 1);
		
	}

	public function is_compatible() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		return true;

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */

	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'gloo_for_elementor' ),
			'<strong>' . esc_html__( 'Otw Content Trimmer', 'gloo_for_elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'gloo_for_elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}


	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'gloo_for_elementor' ),
			'<strong>' . esc_html__( 'Otw Content Trimmer', 'gloo_for_elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'gloo_for_elementor' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'gloo_for_elementor' ),
			'<strong>' . esc_html__( 'Otw Content Trimmer', 'gloo_for_elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'gloo_for_elementor' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function otw_elementor_widgets_modifier($element, $section_id) {
		//echo $element->get_name();
	//	$controls = $element->get_frontend_settings_keys();
	
	//db(\Elementor\Plugin::instance()->widgets_manager->get_widget_types());exit();
	//print_r($controls);
		if ( array_key_exists($element->get_name(), self::$allowedWidgets)) {
		//if ('heading' == $element->get_name() ) {
			
			$element->start_injection( [
				'type' => 'section',
				'at' => 'end',
				'of' => $section_id,
			] );

			$element->add_control(
				'trim_start',
				[
					'type' => \Elementor\Controls_Manager::TEXT,
					'label' => __( 'Trim Start', 'gloo_for_elementor' ),
				]
			);

			$element->add_control(
				'trim_end',
				[
					'type' => \Elementor\Controls_Manager::TEXT,
					'label' => __( 'String Length', 'gloo_for_elementor' ),
				]
			);

			$element->add_control(
				'trim_type',
				[
				'label' => __( 'Trim Type', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Trim Type', 'gloo_for_elementor' ),
					'words' => __( 'Words', 'gloo_for_elementor' ),
					'char' => __( 'Char', 'gloo_for_elementor' ),
					'paragraphs' => __( 'Paragraphs', 'gloo_for_elementor' ),
					]
				]
			);
			
			$element->end_injection();

		}
	}

	public function otw_render_widget_output( $content, $widget ) {
		//$settings = $widget->get_settings_for_display();
		//return $content;
		
		if ( array_key_exists($widget->get_name(), self::$allowedWidgets)) {
			$settings = $widget->get_settings_for_display();
			if(self::$allowedWidgets[$widget->get_name()] == 'unknown')
				$content = $this->widget_content_in_settings($content, $widget );
			else
				$content = $this->widget_content_in_settings($settings[self::$allowedWidgets[$widget->get_name()]], $widget );				
		}
		
		return "<div class='otw_".$widget->get_name()."'>".$content."</div>";

		/*if ( array_key_exists($widget->get_name(), self::$allowedWidgets)) {

			$settings = $widget->get_settings_for_display();
			$trim_start = $widget->get_settings( 'trim_start' );
			$trim_end = $widget->get_settings( 'trim_end' );
			$trim_type = $widget->get_settings( 'trim_type' );

			if ( '' === $settings['title'] ) {
				return;
			}

			$widget->add_render_attribute( 'title', 'class', 'elementor-heading-title' );

			if ( ! empty( $settings['size'] ) ) {
				$widget->add_render_attribute( 'title', 'class', 'elementor-size-' . $settings['size'] );
			}
			
			//$widget->add_inline_editing_attributes( 'title' );
			
			$title = $settings['title'];

			if ( ! empty( $settings['link']['url'] ) ) {
				$widget->add_link_attributes( 'url', $settings['link'] );

				$title = sprintf( '<a %1$s>%2$s</a>', $widget->get_render_attribute_string( 'url' ), $title );
			}

			$title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['header_size'], $widget->get_render_attribute_string( 'title' ), $this->otw_trim_content($title, $trim_start, $trim_end, $trim_type) );

			$content = $title_html;
		}*/
			
		
	}

	public function before_render_content($widget){

		if ( array_key_exists($widget->get_name(), self::$allowedWidgets)) {
			if(self::$allowedWidgets[$widget->get_name()] != 'unknown'){
				
				$settings = $widget->get_settings_for_display();
				//$settings = $widget->parse_dynamic_settings( $widget->get_settings() );
				
				/*if($widget->get_name() == 'theme-post-title'){
					$original_title    = isset( $settings['title'] ) ? $settings['title'] : null;
					var_dump( $original_title );
					$widget->set_settings('title', 'new heading');
					var_dump($widget->get_settings_for_display( 'title' ));
					//wp_die();
				}*/
				//$settings = $widget->get_active_settings();

				//$content = $widget->get_settings(self::$allowedWidgets[$widget->get_name()]);
				$content = $settings[self::$allowedWidgets[$widget->get_name()]];
				//db(self::$allowedWidgets[$widget->get_name()]);
				$content = $this->widget_content_in_settings($content, $widget );
				$widget->set_settings(self::$allowedWidgets[$widget->get_name()], $content);
				//$widget->delete_setting(self::$allowedWidgets[$widget->get_name()]);
				//$widget->render();
				//db(get_class_methods($widget));
				//db($settings);

			}
			//return $widget;
			/*$trim_start = $widget->get_settings( 'trim_start' );
			$trim_end = $widget->get_settings( 'trim_end' );
			$trim_type = $widget->get_settings( 'trim_type' );

			$content = $widget->get_settings(self::$allowedWidgets[$widget->get_name()]);

			if ( empty($content) || empty($trim_type)) {
				return $content;
			}

			if(!$trim_start)
				$trim_start = 0;
			
			if($trim_type && $trim_end){
				$content = wp_strip_all_tags( $content );
				$content = $this->otw_trim_content($content, $trim_start, $trim_end, $trim_type);
				$widget->set_settings(self::$allowedWidgets[$widget->get_name()], $content);
			}*/
		}
	}

	public function otw_trim_content($content, $offset = 0, $limit, $trim_type) {
		//echo $trim_type;
		
	
		if($trim_type == 'paragraphs') {

			if($offset > 0) {
				$content = substr($content,$offset);
			}
	
			$content = $this->otw_trim_paragraphs(substr($content,$offset), $limit, '');

		}
		elseif($trim_type == 'words') {
			$content = wp_strip_all_tags( $content );
			if($offset > 0) {
				$content = substr($content,$offset);
			}
	
			if(function_exists('wp_trim_words')) {
				$content = wp_trim_words(substr($content,$offset), $limit, '')." ...";
			}

		} elseif($trim_type == 'char') {			
			//$content = substr($content, $offset, strpos($content, ' ', $limit));
			$content = wp_strip_all_tags( $content );
			$content = substr($content, $offset, $limit)."...";
		}

		return $content;

	}


	public function widget_content_in_settings($content, $widget ){
		
		
		$trim_start = $widget->get_settings( 'trim_start' );
		$trim_end = $widget->get_settings( 'trim_end' );
		$trim_type = $widget->get_settings( 'trim_type' );
		
		
		$settings = $widget->get_settings_for_display();
		$trimmedContent = $content;
		$originalContent = $content;
		//$trimmedContent = $settings[self::$allowedWidgets[$widget->get_name()]];
		//$originalContent = $settings[self::$allowedWidgets[$widget->get_name()]];
		//$trimmedContent = $widget->get_settings(self::$allowedWidgets[$widget->get_name()]);
		//$originalContent = $widget->get_settings(self::$allowedWidgets[$widget->get_name()]);

		if ( empty($trimmedContent) || empty($trim_type)) {
			return $content;
		}

		if(!$trim_start)
			$trim_start = 0;
		
		if($trim_type && $trim_end){
			
			$trimmedContent = $this->otw_trim_content($trimmedContent, $trim_start, $trim_end, $trim_type);
			$content = str_replace($originalContent, $trimmedContent, $content);
			//$widget->set_settings(self::$allowedWidgets[$widget->get_name()], $content);
		}

		//return wp_kses_post($content);
		return $content;
		
	}

	public function otw_trim_paragraphs( $text, $num_words = 55, $more = null ) {
		
		if( $text){
			preg_match_all ( '#<p>(.+?)</p>#', $text, $parts );
			if($parts && is_array($parts) && count($parts) >= 1){
				if($parts[0] && is_array($parts[0]) && count($parts[0]) >= 1){
					$i = 0;
					$text = '';
					foreach($parts[0] as $key=>$value){
						if($i < $num_words){
							$temp_content = wp_strip_all_tags( $value );
							$text .= '<p>'.$temp_content.'</p>';
						}
						else
							break;
						$i++;
					}
				}
			}
		}    	
		return $text;
		
	}

}
