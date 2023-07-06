<?php
namespace Gloo\Modules\Device_Widget;

use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
/**
 * Elementor OTW Swatch Display Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Widget_Device extends \Elementor\Widget_Base {
	
	private $prefix = 'gloo_device_';

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		
		/* register style */
		wp_register_style('device-widget', gloo()->plugin_url( 'assets/css/device-widget.css' ), [], '1.1' );
		wp_register_style('slick-css', gloo()->plugin_url( 'assets/css/slick.css' ), [], '1.1' );
		/* register script */
		wp_register_script( 'slick-slider', gloo()->plugin_url( 'assets/js/slick.js' ), [ 'elementor-frontend' ], '1.8.0', true );
		wp_register_script( 'device-script', gloo()->plugin_url( 'assets/js/device-script.js' ), [ 'elementor-frontend' ], '1.0.0', true );

	}
	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'gloo_device_widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Devices+Widget', 'gloo_for_elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'gloo-elements-icon-power';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'gloo' ];
	}

	/**
	 * Enqueue styles.
	 */
	public function get_style_depends() {
		return [ 'slick-css', 'device-widget' ];
	}

	/**
	 * Enqueue styles.
	 */
	public function get_script_depends() {
		return [ 'slick-slider', 'device-script' ];
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			$this->prefix,
			[
				'label' => __( 'Device Widget', 'gloo_for_elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			$this->prefix.'device_image',
			[
				'label' => __( 'Device Image', 'gloo_for_elementor' ),
				'type' 	=> Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			$this->prefix.'source_type',
			[
				'label' 		=> __( 'Source Type', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'image',
				'options' 		=> [
					'image'  	=> __( 'Image', 'gloo_for_elementor' ),
					'video'  	=> __( 'Video', 'gloo_for_elementor' ),
					'iframe'  	=> __( 'Iframe', 'gloo_for_elementor' ),
					'gallery'  	=> __( 'Gallery', 'gloo_for_elementor' ),
					'elementor_template'  	=> __( 'Elementor Template', 'gloo_for_elementor' ),
				],
			]
		);

		$this->add_responsive_control(
			$this->prefix.'align',
			[
				'label' 		=> __( 'Alignment', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::CHOOSE,
				'default'		=> 'center',
				'options' 		=> [
					'left' 		=> [
						'title' => __( 'Left', 'gloo_for_elementor' ),
						'icon' 	=> 'eicon-h-align-left',
					],
					'center' 	=> [
						'title' => __( 'Center', 'gloo_for_elementor' ),
						'icon' 	=> 'eicon-h-align-center',
					],
					'right' 	=> [
						'title' => __( 'Right', 'gloo_for_elementor' ),
						'icon' 	=> 'eicon-h-align-right',
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			$this->prefix.'width',
			[
				'label' 		=> __( 'Maximum Width', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'unit' 		=> '%',
					'size' 		=> 100
				],
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1920,
						'step' 	=> 10,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-source-wrapper' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
					'{{WRAPPER}} .gloo-source-wrapper .gloo-device-box' => 'width: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$this->add_responsive_control(
			$this->prefix.'screen_width',
			[
				'label' 		=> __( 'Screen Width', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'unit' 		=> '%',
					'size' 		=> 70
				],
				'range' 		=> [
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
					'px' => [
						'min' 	=> 0,
						'max' 	=> 1000,
					],
					
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-media-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			$this->prefix.'screen_height',
			[
				'label' 		=> __( 'Screen Height', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'unit' 		=> '%',
					'size' 		=> 70
				],
				'range' 		=> [
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
					'px' => [
						'min' 	=> 0,
						'max' 	=> 1000,
					],
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-media-screen' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-device-box' => 'padding-bottom: calc({{SIZE}}{{UNIT}} - 5{{UNIT}});',
				]
			]
		);

		$this->add_responsive_control(
			$this->prefix.'screen_position_y',
			[
				'label' 		=> __( 'Screen Position Y', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'unit' 		=> '%',
					'size' 		=> 50
				],
				'range' 		=> [
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
					'px' => [
						'min' 	=> 0,
						'max' 	=> 1000,
					],
					
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-source-wrapper .gloo-media-wrapper' => 'left: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			$this->prefix.'screen_position',
			[
				'label' 		=> __( 'Screen Position X', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'unit' 		=> '%',
					'size' 		=> 50
				],
				'range' 		=> [
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
					'px' => [
						'min' 	=> 0,
						'max' 	=> 1000,
					],
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-media-wrapper' => 'top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			$this->prefix.'scroll',
			[
				'label' => __( 'Scroll', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' 		=> 'no',
				'label_on' 		=> __( 'Yes', 'gloo_for_elementor' ),
				'label_off' 	=> __( 'No', 'gloo_for_elementor' ),
				'return_value' 	=> 'scroll',
				'prefix_class'	=> 'gloo-device-',
			]
		);

		$this->end_controls_section();

		/* Image section */

		$this->start_controls_section(
			$this->prefix.'image_option',
			[
				'label' 	=> __( 'Image Option', 'gloo_for_elementor' ),
				'condition'	=> [
					$this->prefix.'source_type' => [ 'image' ],
				]
			]
		);

		$this->add_control(
			$this->prefix.'media_portrait_screenshot',
			[
				'label' => __( 'Image', 'gloo_for_elementor' ),
				'type' 	=> Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'	=> [
					$this->prefix.'source_type' => [ 'image' ],
				]
			]
		);
		
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' 			=> $this->prefix.'media_portrait_screenshot',
				'label' 		=> __( 'Screenshot Size', 'gloo_for_elementor' ),
				'default' 		=> 'large',
				'condition'		=> [
					$this->prefix.'media_portrait_screenshot[url]!'	=> '',
					$this->prefix.'source_type' => [ 'image' ],
				]
			]
		);

		$this->end_controls_section();

		/* Video section */

		$this->start_controls_section(
			'video_option',
			[
				'label' 	=> __( 'Video Option', 'gloo_for_elementor' ),
				'condition'	=> [
					$this->prefix.'source_type' => [ 'video' ],
				]
			]
		);

		$this->add_control(
			$this->prefix.'video_setting',
			[
				'label' => __( 'Video Settings', 'gloo_for_elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$this->prefix.'video_type',
			[
				'label' => __( 'Video Type', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => [
					'youtube' => __( 'Youtube', 'gloo_for_elementor' ),
					'vimeo' => __( 'Vimeo', 'gloo_for_elementor' ),
					'hosted' => __( 'Self Hosted', 'gloo_for_elementor' ),
				],
			]
		);

		$this->add_control(
			$this->prefix.'video_url',
			[
				'label' => __( 'Video Url', 'gloo_for_elementor' ),
				'type' => Controls_Manager::URL,
				'default' => 'https://www.youtube.com/watch?v=C0DPdy98e4c',
				'condition' => [
					$this->prefix.'video_type' => ['youtube', 'vimeo'],
				],
			]
		);

		$this->add_control(
			$this->prefix.'autoplay',
			[
				'label' => __( 'Autoplay', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			$this->prefix.'mute',
			[
				'label' => __( 'Mute', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			$this->prefix.'controls',
			[
				'label' => __( 'Controls', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			$this->prefix.'loop',
			[
				'label' => __( 'Loop', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			$this->prefix.'hosted_url',
			[
				'label' => __( 'Choose File', 'gloo_for_elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					$this->prefix.'video_type' => 'hosted',
				],
			]
		);

		$this->end_controls_section();

		/* Iframe section */

		$this->start_controls_section(
			'iframe_option',
			[
				'label' 	=> __( 'Iframe Option', 'gloo_for_elementor' ),
				'condition'	=> [
					$this->prefix.'source_type' => [ 'iframe' ],
				]
			]
		);

		$this->add_control(
			$this->prefix.'is_iframe_url',
			[
				'label' => __( 'Iframe Url', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' 		=> 'no',
				'label_on' 		=> __( 'Yes', 'gloo_for_elementor' ),
				'label_off' 	=> __( 'No', 'gloo_for_elementor' ),
				'return_value' 	=> 'yes',
			]
		);

		$this->add_control(
			$this->prefix.'iframe_url',
			[
				'label' => __( 'Url', 'gloo_for_elementor' ),
				'type' => Controls_Manager::URL,
				'default' => 'https://www.youtube.com/watch?v=C0DPdy98e4c',
				'condition' => [
					$this->prefix.'source_type' => [ 'iframe' ],
					$this->prefix.'is_iframe_url' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			$this->prefix.'iframe_code',
			[
				'label' => __( 'Iframe Code', 'gloo_for_elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 10,
				'placeholder' => __( 'Type your iframe here', 'gloo_for_elementor' ),
				'condition'	=> [
					$this->prefix.'source_type' => [ 'iframe' ],
					$this->prefix.'is_iframe_url!' => [ 'yes' ],
				]
			]
		);

		$this->end_controls_section();

		/* Gallery section */

		$this->start_controls_section(
			'gallery_option',
			[
				'label' 	=> __( 'Gallery Option', 'gloo_for_elementor' ),
				'condition'	=> [
					$this->prefix.'source_type' => [ 'gallery' ],
				]
			]
		);

		$this->add_control(
			$this->prefix.'gallery',
			[
				'label' => __( 'Add Images', 'gloo_for_elementor' ),
				'type' => Controls_Manager::GALLERY,
				'default' => [],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' 			=> $this->prefix.'media_gallery',
				'label' 		=> __( 'Screenshot Size', 'gloo_for_elementor' ),
				'default' 		=> 'large',
				'condition'		=> [
 					$this->prefix.'source_type' => [ 'gallery' ],
				]
			]
		);

		$this->add_responsive_control(
			$this->prefix.'gallery_height',
			[
				'label' 		=> __( 'Gallery Height', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'unit' 		=> 'px',
					'size' 		=> 350
				],
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 5,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
					'vh' => [
						'min' 	=> 0,
						'max' 	=> 1000,
					],
				],
				'size_units' 	=> [ 'px', '%', 'vh'],
				'selectors' 	=> [
					'{{WRAPPER}} .slick-wrapper' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-slide' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-slide im ' => 'height: {{SIZE}}{{UNIT}};'
				]
			]
		);


		$this->add_control(
			$this->prefix.'gallery_navigation',
			[
				'label' => __( 'Navigation', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both' => __( 'Arrows and Dots', 'gloo_for_elementor' ),
					'arrows' => __( 'Arrows', 'gloo_for_elementor' ),
					'dots' => __( 'Dots', 'gloo_for_elementor' ),
					'none' => __( 'None', 'gloo_for_elementor' ),
				],
			]
		);

		$this->add_control(
			$this->prefix.'gallery_autoplay',
			[
				'label' => __( 'Autoplay', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			$this->prefix.'gallery_autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'gloo_for_elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'frontend_available' => true,
				'condition' => [
					$this->prefix.'gallery_autoplay' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide' => 'transition-duration: calc({{VALUE}}ms*1.2)',
				],
			]
		);

		$this->add_control(
			$this->prefix.'gallery_infinite',
			[
				'label' => __( 'Infinite Loop', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true
			]
		);

		$this->add_control(
			$this->prefix.'gallery_transition',
			[
				'label' => __( 'Transition', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'gloo_for_elementor' ),
					'fade' => __( 'Fade', 'gloo_for_elementor' ),
				],
				'frontend_available' => true
			]
		);

		$this->add_control(
			$this->prefix.'gallery_transition_speed',
			[
				'label' => __( 'Transition Speed', 'gloo_for_elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'elementor_option',
			[
				'label' 	=> __( 'Elementor Template', 'gloo_for_elementor' ),
				'condition'	=> [
					$this->prefix.'source_type' => [ 'elementor_template' ],
				]
			]
		);

		$templates = [];

		$args = array(
			'numberposts' => - 1,
			'post_type'   => 'elementor_library'
		);

		$template_posts = get_posts( $args );	

		if ( $template_posts ) {
			foreach ( $template_posts as $template_post ) {
				$title      = get_the_title( $template_post->ID );
				$templates[$template_post->ID] = $title;
			}
		}

		$this->add_control(
			$this->prefix.'template',
			[
				'label' => __( 'Template', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $templates
			]
		);

		$this->end_controls_section();

		/* style section */
		$this->start_controls_section(
			$this->prefix.'shadow',
			[
				'label' => __( 'Shadow', 'gloo_for_elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			$this->prefix.'shadow_enable',
			[
				'label' => __( 'Shadow', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' 		=> 'no',
				'label_on' 		=> __( 'On', 'gloo_for_elementor' ),
				'label_off' 	=> __( 'Off', 'gloo_for_elementor' ),
				'return_value' 	=> 'on',
			]
		);

		$this->add_control(
			$this->prefix.'shadow_custom',
			[
				'label' => __( 'Custom Image', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' 		=> 'no',
				'label_on' 		=> __( 'On', 'gloo_for_elementor' ),
				'label_off' 	=> __( 'Off', 'gloo_for_elementor' ),
				'return_value' 	=> 'on',
				'condition' => [
					$this->prefix.'shadow_enable' => 'on',
				],
			]
		);

		$this->add_control(
			$this->prefix.'shadow_image',
			[
				'label' => __( 'Choose File', 'gloo_for_elementor' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'condition' => [
					$this->prefix.'shadow_enable' => 'on',
					$this->prefix.'shadow_custom' => 'on'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' 			=> $this->prefix.'shadow_image',
				'label' 		=> __( 'Image Size', 'gloo_for_elementor' ),
				'default' 		=> 'large',
				'condition'		=> [
					$this->prefix.'shadow_image[url]!'	=> '',
					$this->prefix.'shadow_custom' => 'on'
				]
			]
		);

		$this->add_responsive_control(
			$this->prefix.'shadow_width',
			[
				'label' 		=> __( 'Shadow Width', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'unit' 		=> 'px',
					'size' 		=> 100
				],
				'range' 		=> [
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
					'px' => [
						'min' 	=> 0,
						'max' 	=> 1000,
					],
					
				],
				'size_units' 	=> [ 'px', '%' ],
				'condition' => [
					$this->prefix.'shadow_enable' => 'on',
				],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-shadow img' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			$this->prefix.'shadow_distance',
			[
				'label' 		=> __( 'Distance', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'unit' 		=> 'px',
					'size' 		=> 10
				],
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 100,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', '%' ],
				'condition' => [
					$this->prefix.'shadow_enable' => 'on',
				],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-shadow' => 'margin-top: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			$this->prefix.'shadow_opacity',
			[
				'label' 		=> __( 'Opacity', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> 0.2,
				'condition' => [
					$this->prefix.'shadow_enable' => 'on',
				],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-shadow' => 'opacity: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			$this->prefix.'shadow_z_index',
			[
				'label' 		=> __( 'Z Index', 'gloo_for_elementor' ),
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> 0.2,
				'condition' => [
					$this->prefix.'shadow_enable' => 'on',
				],
				'selectors' 	=> [
					'{{WRAPPER}} .gloo-shadow' => 'z-index: {{VALUE}};',
				]
			]
		);
		
		$this->end_controls_section();
	}

	protected function get_image_html( $field_control ) {
		$settings = $this->get_settings_for_display();

		if ( isset($settings[$field_control]['url']) && !empty($settings[$field_control]['url']) ) { ?>
			<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, $field_control ); ?>
		<?php }
	}

	public function get_video_id($video_url, $video_type) {

		if($video_type == 'youtube') {

			if(!empty($video_url)) {
				$video_id = explode("?v=", $video_url);
				$video_id = $video_id[1];
			}
		} else if( $video_type == 'vimeo' ) {
			$regs = array();
			if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $video_url, $regs)) {
				$video_id = $regs[3];
			}
		}

		return $video_id;
	}

	public function get_video_html($video_type = '') {
		
		$settings = $this->get_settings_for_display();

		if($video_type == 'youtube' || $video_type == 'vimeo') {

			if ( ! empty( $settings[$this->prefix.'video_url'] ) ) {
				$video_url = $settings[$this->prefix.'video_url']['url'];
			}
			
			$video_id = $this->get_video_id( $video_url , $video_type );
			
			if($video_type == 'youtube') {
				
				$params = array(
					'autoplay' => 0,
					'mute' => 0,
					'controls' => 0,
					'rel' => 0,
					'modestbranding' => 0
				);

				$base_url = 'https://www.youtube.com/embed/'.$video_id;
				
				if( 'yes' == $settings[$this->prefix.'autoplay'] ) {
					$params['autoplay'] = 1;
				}
				
				if( 'yes' == $settings[$this->prefix.'mute'] ) {
					$params['mute'] = 1;
				}
				
				if( 'yes' == $settings[$this->prefix.'controls'] ) {
					$params['controls'] = 1;
				}

				if( 'yes' == $settings[$this->prefix.'loop'] ) {
					$params['loop'] = 1;
				}

				$video_url = $base_url.'?'.http_build_query($params); 

			} else if($video_type == 'vimeo') {

				$params = array(
					'autoplay' => 0,
					'muted' => 0,
					'controls' => 0,
				);

				$base_url = 'https://player.vimeo.com/video/'.$video_id;

				if( 'yes' == $settings[$this->prefix.'autoplay'] ) {
					$params['autoplay'] = 1;
				}
				
				if( 'yes' == $settings[$this->prefix.'mute'] ) {
					$params['muted'] = 1;
				}
				
				if( 'yes' == $settings[$this->prefix.'controls'] ) {
					$params['controls'] = 1;
				}

				if( 'yes' == $settings[$this->prefix.'loop'] ) {
					$params['loop'] = 1;
				}

				$video_url = $base_url.'?'.http_build_query($params);
			} 

		} elseif($video_type == 'hosted') {
			$params = array();
			$hosted_url = $settings[$this->prefix.'hosted_url'];
			$options = ' ';

			if( 'yes' == $settings[$this->prefix.'autoplay'] ) {
				$options .= 'autoplay ';
			}
			
			if( 'yes' == $settings[$this->prefix.'mute'] ) {
				$options .= 'muted ';
			}
			
			if( 'yes' == $settings[$this->prefix.'controls'] ) {
				$options .= 'controls ';
			}
			
			if( 'yes' == $settings[$this->prefix.'loop'] ) {
				$options .= 'loop ';
			}
		} 
		
		$this->add_render_attribute( [
			'gloo-video-wrapper' => [
				'class' => [
					'gloo-video',
				],
			],
		] ); ?>
		<div <?php echo $this->get_render_attribute_string( 'gloo-video-wrapper' ); ?>>
			<?php if($video_type == 'hosted') : ?>
				<video <?php echo $options; ?>>
					<source src="<?php echo $hosted_url['url']; ?>" type="video/mp4">
					Your browser does not support HTML video.
				</video>
			<?php else: 
				$this->get_iframe_code($video_url); ?>
			<?php endif; ?>
		</div>
		<?php 
	}

	public function get_iframe_code($url = '') { 
		
		$this->add_render_attribute( [
			'gloo-source-iframe' => [
				'class' => [
					'gloo-source-iframe',
				],
			],
		] ); ?>

	  	<div <?php echo $this->get_render_attribute_string( 'gloo-source-iframe' ); ?>>
			<iframe width="100%" height="100%" src="<?php echo $url; ?>"></iframe>
		</div>
		<?php
	}

	public function get_gallery_html() {
		$settings = $this->get_settings_for_display();
		$gallery = $settings[$this->prefix.'gallery'];

		$this->add_render_attribute( [
			'gloo-source-gallery' => [
				'class' => [
					'gloo-source-gallery',
				],
			],
		] ); 
		
		?>
		<div <?php echo $this->get_render_attribute_string( 'gloo-source-gallery' ); ?> <?php echo $this->get_render_attribute_string( 'gloo-gallery-settings' ); ?>>
			<?php 

			$show_dots = ( in_array( $settings[$this->prefix.'gallery_navigation'], [ 'dots', 'both' ] ) );
			$show_arrows = ( in_array( $settings[$this->prefix.'gallery_navigation'], [ 'arrows', 'both' ] ) );
			
			$slides_count = count( $settings[$this->prefix.'gallery'] );

			if(!empty($gallery)) : ?>
				<div class="slick-container">
					<!-- Additional required wrapper -->
					<div class="slick-wrapper">
						<!-- Slides -->
						<?php foreach($gallery as $item): ?>
							<div class="swiper-slide">
								<img src="<?php echo Group_Control_Image_Size::get_attachment_image_src($item['id'], $this->prefix.'media_gallery', $settings); ?>" />
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php		
	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		
		$this->add_render_attribute( [
			'gloo-device-wrapper' => [
				'class' => [ 
					'gloo-source-wrapper',
				],
			],
			'gloo-device-box' => [
				'class' => [ 
					'gloo-device-box'
				],
			],
			'gloo-device-shape' => [
				'class' => [ 
					'gloo-device-shape'
				],
			],
			'gloo-media-wrapper' => [
				'class' => [ 
					'gloo-media-wrapper'
				],
			],
			'gloo-media-screen' => [
				'class' => [ 
					'gloo-media-screen'
				],
			],
			'gloo-media-screen-item' => [
				'class' => [ 
					'gloo-media-screen-item'
				],
			],
			'gloo-media-screen-capture' => [
				'class' => [ 
					'gloo-media-screen-capture'
				],
			],
			'gloo-media-screen-content' => [
				'class' => [ 
					'gloo-media-screen-content'
				],
			],
			'gloo-shadow' => [
				'class' => [ 
					'gloo-media-screen-content'
				],
			]
		] ); 

		if ( $settings[$this->prefix.'shadow_enable'] == 'on' ) {
			$this->add_render_attribute( [
				'gloo-shadow' => [
					'class' => [ 
						'gloo-shadow'
					],
				]
			]);
		} ?>
		<div <?php echo $this->get_render_attribute_string( 'gloo-device-wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string('gloo-device-box'); ?>>
				<div <?php echo $this->get_render_attribute_string('gloo-device-shape'); ?>>
					<?php $shape = $settings[$this->prefix.'device_image']; 
					if(!empty($shape) && isset($shape['url'])) { ?>
						<img src="<?php echo $shape['url']; ?>" /> 
					<?php } ?>
				</div>

				<div <?php echo $this->get_render_attribute_string( 'gloo-media-wrapper' ); ?>>
					<div <?php echo $this->get_render_attribute_string('gloo-media-screen'); ?>>
						<div <?php echo $this->get_render_attribute_string('gloo-media-screen-item'); ?>>
							<div <?php echo $this->get_render_attribute_string('gloo-media-screen-capture'); ?>>
								<div <?php echo $this->get_render_attribute_string('gloo-media-screen-content'); ?>>
									<?php 
									if($settings[$this->prefix.'source_type'] == 'image'): 
										$this->get_image_html($this->prefix.'media_portrait_screenshot'); 
									elseif($settings[$this->prefix.'source_type'] == 'video') : 
										$this->get_video_html($settings[$this->prefix.'video_type']); 
									elseif($settings[$this->prefix.'source_type'] == 'iframe') : 
										$is_iframe_url = $settings[$this->prefix.'is_iframe_url']; 
									
										if($is_iframe_url == 'yes' && !empty($settings[$this->prefix.'iframe_url']['url'])) :
											$this->get_iframe_code($settings[$this->prefix.'iframe_url']['url']); 
										elseif(!empty($settings[$this->prefix.'iframe_code'])) :
											echo $settings[$this->prefix.'iframe_code'];
										endif; 

									elseif($settings[$this->prefix.'source_type'] == 'gallery') : 									
										$this->get_gallery_html(); 
									elseif($settings[$this->prefix.'source_type'] == 'elementor_template') : 	
										if(($template_id = $settings[$this->prefix.'template']) && !empty($template_id)) : 
											echo do_shortcode( "[elementor-template id='$template_id']" );
										endif;
									endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if($settings[$this->prefix.'shadow_enable'] == 'on') :?>
				<div <?php echo $this->get_render_attribute_string( 'gloo-shadow' ); ?>>
					<?php if($settings[$this->prefix.'shadow_custom'] == 'on') {
						$this->get_image_html($this->prefix.'shadow_image');
					} else { ?>
						<img src="<?php echo gloo()->plugin_url( 'assets/images/admin/gloo-shade.png'); ?>" /> 
					<?php } ?>
				</div>	
			<?php endif; ?>
		</div>
		<?php
	}

}