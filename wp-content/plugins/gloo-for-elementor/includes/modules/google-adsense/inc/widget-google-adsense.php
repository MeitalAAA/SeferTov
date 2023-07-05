<?php
namespace Gloo\Modules\Google_Adsense_Widget;

/**
 * Elementor OTW Swatch Display Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Google_Adsense extends \Elementor\Widget_Base {
	
	private $prefix = 'gloo';

	public static $slug = 'gloo_for_elementor';

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style('adsense-widget', gloo()->plugin_url( 'assets/css/adsense-widget.css' ), [], '1.1' );

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
		return 'gloo_google_adsense';
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
		return __( 'Google Adsense', self::$slug);
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
		return array( 'adsense-widget' );
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
			$this->prefix.'content_section',
			[
				'label' => __( 'Content', self::$slug ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$sources = [
			'square_and_rectangle' => array(
				'label'   => __( 'Square and rectangle', 'gloo_for_elementor' ),
				'options' => [
					'200x200' => '200 × 200 Small square',
					'240x400' => '240 × 400 Vertical rectangle',
					'250x250' => '250 × 250 Square',
					'250x360' => '250 × 360 Triple widescreen',
					'300x250' => '300 × 250 Inline rectangle',
					'336x280' => '336 × 280 Large rectangle',
					'580x400' => '580 × 400 Netboard',
				]
			),
			'skyscraper' => array(
				'label'   => __( 'Skyscraper', 'gloo_for_elementor' ),
				'options' => [
					'120x600' => '120 × 600 Skyscraper',
					'160x600' => 	'160 × 600 Wide skyscraper',
					'300x600' => '300 × 600 Half-page ad',
					'300x1050' => '300 × 1050	Portrait'
				]
			),
			'leaderboard' => array(
				'label'   => __( 'Leaderboard', 'gloo_for_elementor' ),
				'options' => [
					'468x60' => '468 × 60 Banner',
					'728x90' => 	'728 × 90 Leaderboard',
					'930x180' => '930 × 180 Top banner',
					'970x90' => '970 × 90	Large leaderboard',
					'970x250' => '970 × 250 Billboard',
					'980x120' => '980 × 120 Panorama'
				]
			),
			'mobile' => array(
				'label'   => __( 'Mobile', 'gloo_for_elementor' ),
				'options' => [
					'300x50' => '300 × 50	Mobile banner',
					'320x50' => '320 × 50	Mobile banner',
					'320x100' => '320 × 100 Large mobile banner'
				]
			),
		];


		$this->add_control(
			$this->prefix.'adsense_desktop_size',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Desktop Size', 'gloo_for_elementor' ),
				'separator' => 'before',
				'groups'     => $sources,
			)
		);

		$this->add_control(
			$this->prefix.'adsense_desktop_id',
			array(
				'label'   => __( 'Data ad slot', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			$this->prefix.'adsense_tablet_size',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Tablet Size', 'gloo_for_elementor' ),
				// 'label_block' => true,
				'separator' => 'before',
				'groups'     => $sources,
			)
		);

		$this->add_control(
			$this->prefix.'adsense_tablet_id',
			array(
				'label'   => __( 'Data ad slot', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			$this->prefix.'adsense_mobile_size',
			array(
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Mobile Size', 'gloo_for_elementor' ),
				// 'label_block' => true,
				'separator' => 'before',
				'groups'     => $sources,
			)
		);

		$this->add_control(
			$this->prefix.'adsense_mobile_id',
			array(
				'label'   => __( 'Data ad slot', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			)
		);

		// $this->add_control(
		// 	$this->prefix.'adsense_code',
		// 	[
		// 		'label' => __( 'Ad Code', self::$slug ),
		// 		'type' => \Elementor\Controls_Manager::TEXTAREA,
		// 		'rows' => 10,
		// 	]
		// );

		$this->end_controls_section();
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
		$options = get_option( 'gloo_adsense' ); 

		if( isset( $options['data_ad_client'] ) && !empty( $options['data_ad_client'] ) ) {

			if( ( isset( $settings[ $this->prefix.'adsense_desktop_size' ] )  && !empty( $settings[ $this->prefix.'adsense_desktop_id' ] ) ) && ( isset( $settings[ $this->prefix.'adsense_desktop_id' ] )  && !empty( $settings[ $this->prefix.'adsense_desktop_id' ] ) ) ) {
				$sizes = explode( 'x' ,$settings[ $this->prefix.'adsense_desktop_size' ] );
				$ad_slot = $settings[ $this->prefix.'adsense_desktop_id' ];

				echo '<ins class="adsbygoogle elementor-hidden-phone elementor-hidden-tablet"
				style="width:'.$sizes[0].'px;height:'.$sizes[1].'px"
				data-ad-client="'.$options['data_ad_client'].'"
				data-ad-slot="'.$ad_slot.'"></ins>';
				echo '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
			} 

			if( ( isset( $settings[ $this->prefix.'adsense_tablet_size' ] )  && !empty( $settings[ $this->prefix.'adsense_tablet_size' ] ) ) && ( isset( $settings[ $this->prefix.'adsense_tablet_id' ] )  && !empty( $settings[ $this->prefix.'adsense_tablet_id' ] ) ) ) {
				$sizes = explode( 'x' ,$settings[ $this->prefix.'adsense_tablet_size' ] );
				$adsense_tablet_id = $settings[ $this->prefix.'adsense_tablet_id' ];

				echo '<ins class="adsbygoogle elementor-hidden-desktop elementor-hidden-phone"
				style="width:'.$sizes[0].'px;height:'.$sizes[1].'px"
				data-ad-client="'.$options['data_ad_client'].'"
				data-ad-slot="'.$adsense_tablet_id.'"></ins>';
				echo '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
			}
			
			if( ( isset( $settings[ $this->prefix.'adsense_mobile_size' ] )  && !empty( $settings[ $this->prefix.'adsense_mobile_size' ] ) ) && ( isset( $settings[ $this->prefix.'adsense_tablet_id' ] )  && !empty( $settings[ $this->prefix.'adsense_tablet_id' ] ) ) ) {
				$sizes = explode( 'x' ,$settings[ $this->prefix.'adsense_mobile_size' ] );
				$adsense_mobile_id = $settings[ $this->prefix.'adsense_mobile_id' ];

				echo '<ins class="adsbygoogle elementor-hidden-desktop elementor-hidden-tablet"
				style="width:'.$sizes[0].'px;height:'.$sizes[1].'px"
				data-ad-client="'.$options['data_ad_client'].'"
				data-ad-slot="'.$adsense_mobile_id.'"></ins>';
				echo '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
			}
		}
	}

}