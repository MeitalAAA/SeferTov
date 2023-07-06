<?php
namespace Gloo\Modules\Schema_Control;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DynamicTags;
use Elementor\Repeater;
use Elementor\Element_Base;

class Settings {
	private $prefix = 'gloo_sc_';
 
	public function __construct() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_controls' ], 30, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'render_schema_section' ] );
	}

	public function register_controls( $element, $section_id ) {

		if ( ! $element instanceof Element_Base ) {
			return;
		}

		if ( ! in_array( $element->get_type(), ['widget'] ) ) {
			return;
		}

		$stack = \Elementor\Plugin::$instance->controls_manager->get_element_stack( $element );

		$all_controls = $element->get_controls();
 		//$settings = $element->get_settings_for_display();	
		// echo '<pre>';
		// print_r($stack);
		// echo '</pre>';
 
		$element->start_controls_section(
			$this->prefix . 'schema',
			[
				'label' => __( 'Schema Control', 'gloo_for_elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			$this->prefix . 'schema_activate',
			[
				'label'        => __( 'Active', 'gloo_for_elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Active', 'gloo_for_elementor' ),
				'label_off'    => __( 'Off', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'off',
			]
		);

		$element->add_control(
			$this->prefix .'total_time',
			array(
				'label'   => __( 'Total Time', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'dynamic'   => [
					'active' => true,
				],
				'default' => '',
				'condition' => [
					$this->prefix . 'type' => 'HowTo',
					$this->prefix . 'schema_activate' => 'yes'
				]
			)
		);

		$element->add_control(
			$this->prefix . 'type',
			[
				'label'     => __( 'Schema Type', 'gloo_for_elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'FAQPage',
				'options' => [
					'FAQPage'  => __( 'FAQ', 'gloo_for_elementor' ),
					'HowTo'  => __( 'How To', 'gloo_for_elementor' ),
					'Review'  => __( 'Reviews', 'gloo_for_elementor' ),
				],
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes'
				],
			]
		);

		$da_repeater = new Repeater();			 

		$da_repeater->add_control(
			$this->prefix . 'question',
			array(
				'type'        => Controls_Manager::TEXTAREA,
				'label'       => __( 'Question', 'gloo_for_elementor' ),
				'dynamic'     => array(
					'active'     => true,
				),
			)
		);

		$da_repeater->add_control(
			$this->prefix . 'answer',
			array(
				'type'        => Controls_Manager::TEXTAREA,
				'label'       => __( 'Answer', 'gloo_for_elementor' ),
				'dynamic'     => array(
					'active'     => true,
				),
			)
		);

		$element->add_control(
			$this->prefix . 'schema_repeater',
			[
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields'        => $da_repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'question}}}',
				'label_block'   => true,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'FAQPage'
				],

			]
		);

		/* how to schema controls */

		$element->add_control(
			$this->prefix . 'step_title',
			[
				'label' => __( 'Title', 'gloo_for_elementor' ),
				'label_block'   => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'HowTo'
				],
 			]
		);
 
		$element->add_control(
			$this->prefix . 'step_main_image',
			[
				'label' => __( 'Choose Image', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'HowTo'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'step_main_dimension',
			[
				'label' => __( 'Image Dimension', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
				'separator' => 'after',
				'default' => [
					'width' => '406',
					'height' => '305',
				],
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'HowTo'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'step_estimated_cost',
			[
				'label' => __( 'Estimated Cost', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 100,
				'placeholder' => __( '100', 'gloo_for_elementor' ),
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'HowTo'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'step_currency',
			[
				'label' => __( 'Currency', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'USD', 'gloo_for_elementor' ),
				'placeholder' => __( 'USD', 'gloo_for_elementor' ),
				'separator' => 'after',
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'HowTo'
				],
			]
		);

		/* supply repeater */

		$supply_repeater = new Repeater();			 

		$supply_repeater->add_control(
			$this->prefix . 'supply_name',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Name', 'gloo_for_elementor' ),
				'dynamic'     => array(
					'active'     => true,
				),
			)
		); 

		$element->add_control(
			$this->prefix . 'supply_repeater',
			[
				'label' => __( 'Supply', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'separator' => 'after',
				'fields'        => $supply_repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'supply_name}}}',
				'label_block'   => true,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'HowTo'
				],
			]
		);

		/* tool repeater */

		$tool_repeater = new Repeater();			 

		$tool_repeater->add_control(
			$this->prefix . 'tool_name',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Name', 'gloo_for_elementor' ),
				'dynamic'     => array(
					'active'     => true,
				),
			]
		); 

		$element->add_control(
			$this->prefix . 'tool_repeater',
			[
				'label' => __( 'Tool', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'separator' => 'after',
				'fields'        => $tool_repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'tool_name}}}',
				'label_block'   => true,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'HowTo'
				],

			]
		);

		/* step repeater */
		$step_repeater = new Repeater();			 

		$step_repeater->add_control(
			$this->prefix . 'step_url',
			[
				'label' => __( 'Url', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'gloo_for_elementor' ),
 				'dynamic'     => array(
					'active'     => true,
				),
			]
		); 

		$step_repeater->add_control(
			$this->prefix . 'step_name',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Name', 'gloo_for_elementor' ),
				'label_block' => true,
				'dynamic'     => array(
					'active'     => true,
				),
			]
		); 

		$step_repeater->add_control(
			$this->prefix . 'step_item_list',
			[
				'label'    => __( 'List Element', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 10,
				'default' => __( 'Default description', 'gloo_for_elementor' ),
				'placeholder' => __( 'Type your description here', 'gloo_for_elementor' ),
				'dynamic'     => array(
					'active'     => true,
				),
			]
		); 

		$step_repeater->add_control(
			$this->prefix . 'step_image',
			[
				'label' => __( 'Choose Image', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		); 

		$step_repeater->add_control(
			$this->prefix . 'step_image_dimension',
			[
				'label' => __( 'Image Dimension', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
				'default' => [
					'width' => '406',
					'height' => '305',
				],
			]
		);

		$element->add_control(
			$this->prefix . 'step_repeater',
			[
				'label' => __( 'Step Items', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'separator' => 'after',
				'fields'        => $step_repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'step_name}}}',
				'label_block'   => true,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'HowTo'
				],
			]
		);

		/* Review schema control */
		$options = array(
			'Book'  => 'Book',
			'Course' => 'Course',
			'CreativeWorkSeason' => 'CreativeWorkSeason',
			'CreativeWorkSeries' => 'CreativeWorkSeries',
			'Episode' => 'Episode',
			'Event' => 'Event',
			'Game' => 'Game',
			'HowTo' => 'HowTo',
			'LocalBusiness' => 'LocalBusiness',
			'MediaObject' => 'MediaObject',
			'Movie' => 'Movie',
			'MusicPlaylist' => 'MusicPlaylist',
			'MusicRecording' => 'MusicRecording',
			'Organization' => 'Organization',
			'Product' => 'Product',
			'Recipe' => 'Recipe',
			'SoftwareApplication' => 'SoftwareApplication'
		);

		$element->add_control(
			$this->prefix . 'review_type',
			[
				'label' => __( 'Type Value', 'gloo_for_elementor' ),
 				'label_block'   => false,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $options,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'review_name',
			[
				'label' => __( 'Name', 'gloo_for_elementor' ),
				'label_block'   => false,
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'brand_name',
			[
				'label' => __( 'Brand', 'gloo_for_elementor' ),
				'label_block'   => false,
				'description' => __( 'Optional', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'offer_low_price',
			[
				'label' => __( 'Offer Low Price', 'gloo_for_elementor' ),
				'label_block'   => false,
				'description' => __( 'Optional', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'offer_high_price',
			[
				'label' => __( 'Offer High Price', 'gloo_for_elementor' ),
				'label_block'   => false,
				'description' => __( 'Optional', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'offer_currency',
			[
				'label' => __( 'Offer Currency', 'gloo_for_elementor' ),
				'label_block'   => false,
				'description' => __( 'Optional', 'gloo_for_elementor' ),
				'placeholder' => esc_html__( 'USD', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix .'hr_images',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$element->add_control(
			$this->prefix .'review_images',
			[
				'label' => esc_html__( 'Images', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'description' => __( 'Optional', 'gloo_for_elementor' ),
				'show_label' => true,
				'default' => [],
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix .'hr_aggregate',
			[   
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$element->add_control(
			$this->prefix . 'rating_value',
			[
				'label' => __( 'Aggregate Rating Value', 'gloo_for_elementor' ),
				'label_block'   => false,
				'type' => \Elementor\Controls_Manager::NUMBER,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

 		$element->add_control(
			$this->prefix . 'rating_count',
			[
				'label' => __( 'Aggregate Rating Count', 'gloo_for_elementor' ),
				'label_block'   => false,
				'type' => \Elementor\Controls_Manager::NUMBER,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix . 'best_rating',
			[
				'label' => __( 'Aggregate Best Rating', 'gloo_for_elementor' ),
				'label_block'   => false,
				'description' => __( 'Optional', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		$element->add_control(
			$this->prefix .'reviews',
			[
				'label' => esc_html__( 'Reviews ?', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'textdomain' ),
				'label_off' => esc_html__( 'No', 'textdomain' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review'
				],
			]
		);

		/* review repeater */
		$review_repeater = new Repeater();			 

		$review_repeater->add_control(
			$this->prefix . 'rating',
			[
				'label' => __( 'Review Rating', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => false,
  				'dynamic'     => array(
					'active'     => true,
				),
			]
		); 

		$review_repeater->add_control(
			$this->prefix . 'author',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Review Author', 'gloo_for_elementor' ),
				'label_block' => false,
				'dynamic'     => array(
					'active'     => true,
				),
			]
		); 

		$element->add_control(
			$this->prefix . 'review_repeater',
			[
				'label' => __( 'Reviews List', 'gloo_for_elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'separator' => 'after',
				'fields'        => $review_repeater->get_controls(),
				'title_field'   => '{{{' . $this->prefix . 'rating}}}',
				'label_block'   => true,
				'condition' => [
					$this->prefix . 'schema_activate' => 'yes',
					$this->prefix . 'type' => 'Review',
					$this->prefix .'reviews' => 'yes'
				],
			]
		);

		$element->end_controls_section();
		
	 }
	  
	 public function render_schema_section( Element_Base $element ) {
		$settings = $element->get_settings_for_display(); 

		/* Schema FAQ */
		if( isset($settings[$this->prefix . 'schema_activate']) && 'yes' == $settings[$this->prefix . 'schema_activate'] ) { 
			
			if( isset($settings[$this->prefix . 'type']) && 'FAQPage' == $settings[$this->prefix . 'type'] ) { 

				if( isset( $settings[ $this->prefix . 'schema_repeater' ] ) && !empty( $settings[ $this->prefix . 'schema_repeater' ] ) ) {
					
					$schema = [
						'@context'   => "https://schema.org",
						'@type'      => $settings[$this->prefix . 'type'],
						'mainEntity' => array()
					];
	
					foreach( $settings[ $this->prefix . 'schema_repeater' ] as $schema_item ) {
						$questions = [
							'@type'          => 'Question',
							'name'           => $schema_item[ $this->prefix . 'question' ],
							'acceptedAnswer' => [
								'@type' => "Answer",
								'text' => $schema_item[ $this->prefix . 'answer' ]
							]
						];
	
						array_push($schema['mainEntity'], $questions);
					}

					echo '<script type="application/ld+json">'. json_encode($schema) .'</script>';
				}
			}

			if( isset($settings[$this->prefix . 'type']) && 'HowTo' == $settings[$this->prefix . 'type'] ) { 

				$schema = [
					'@context' => "http://schema.org",
					'@type'      => $settings[$this->prefix . 'type'],
				];

				if( isset( $settings[$this->prefix . 'step_title'] ) && !empty( $settings[$this->prefix . 'step_title'] )) {
					$schema['name'] = $settings[$this->prefix . 'step_title'];
				}

				if( isset( $settings[$this->prefix . 'step_main_image'] ) && !empty( $settings[$this->prefix . 'step_main_image']['url'] ) ) {

					$schema['image']  = [
						'@type' =>  'ImageObject',
						'url' => $settings[$this->prefix . 'step_main_image']['url'],
					];

					if( isset( $settings[$this->prefix . 'step_main_dimension'] ) && !empty( $settings[$this->prefix . 'step_main_dimension'] )) {
						$dimension = $settings[$this->prefix . 'step_main_dimension'];

						$schema['image']['height'] = $dimension['height'];
						$schema['image']['width'] = $dimension['width'];
					}
 
				}

				if( isset( $settings[$this->prefix . 'step_estimated_cost'] ) && !empty( $settings[$this->prefix . 'step_estimated_cost'] ) ) {

					$schema['estimatedCost']  = [
						'@type' => 'MonetaryAmount',
						'currency' => ( isset( $settings[$this->prefix . 'step_currency'] ) && !empty( $settings[$this->prefix . 'step_currency'] ) ) ? $settings[$this->prefix . 'step_currency'] : 'USD',
						'value' => $settings[$this->prefix . 'step_estimated_cost']
					];
				}

				if( isset( $settings[ $this->prefix . 'supply_repeater' ] ) && !empty( $settings[ $this->prefix . 'supply_repeater' ] ) ) {
	
					foreach( $settings[ $this->prefix . 'supply_repeater' ] as $supply_item ) {
						
						$schema['supply'][] = [
							'@type' => 'HowToSupply',
							'name' => ( isset( $supply_item[$this->prefix . 'supply_name'] ) && !empty( $supply_item[$this->prefix . 'supply_name'] ) ) ? $supply_item[$this->prefix . 'supply_name'] : '',
						];
					}
				}

				if( isset( $settings[ $this->prefix . 'tool_repeater' ] ) && !empty( $settings[ $this->prefix . 'tool_repeater' ] ) ) {
	
					foreach( $settings[ $this->prefix . 'tool_repeater' ] as $tool_item ) {
						
						$schema['tool'][] = [
							'@type' => 'HowToTool',
							'name' => ( isset( $tool_item[$this->prefix . 'tool_name'] ) && !empty( $tool_item[$this->prefix . 'tool_name'] ) ) ? $tool_item[$this->prefix . 'tool_name'] : '',
						];
					}
				}

				if( isset( $settings[ $this->prefix . 'step_repeater' ] ) && !empty( $settings[ $this->prefix . 'step_repeater' ] ) ) {
					$steps = [];
					
					foreach( $settings[ $this->prefix . 'step_repeater' ] as $step_item ) {
						
						$steps = [
							'@type' => 'HowToStep',
							'url' => ( isset( $step_item[$this->prefix . 'step_url'] ) && !empty( $step_item[$this->prefix . 'step_url'] ) ) ? $step_item[$this->prefix . 'step_url'] : '',
							'name' => ( isset( $step_item[$this->prefix . 'step_name'] ) && !empty( $step_item[$this->prefix . 'step_name'] ) ) ? $step_item[$this->prefix . 'step_name'] : '',
						];

						if( isset( $step_item[$this->prefix . 'step_item_list'] ) && !empty( $step_item[$this->prefix . 'step_item_list'] ) ) {
							
							$lines = explode("\n", str_replace("\r", "", $step_item[$this->prefix . 'step_item_list']));

							if( !empty( $lines ) ) {
								
								$type = 'HowToDirection';
								
								foreach( $lines as $line ) {
									
									if( $line == '*') {
										$type = 'HowToTip';
										continue;
									}

									$steps['itemListElement'][] = [
										'@type' => $type,
										'text' => $line
									];

									$type = 'HowToDirection';
								}
							}
 						}

						if( isset( $step_item[$this->prefix . 'step_image'] ) && !empty( $step_item[$this->prefix . 'step_image']['url'] ) ) {
							$steps['image']  = [
								'@type' =>  'ImageObject',
								'url' => $step_item[$this->prefix . 'step_image']['url'],
							];


							if( isset( $step_item[$this->prefix . 'step_image_dimension'] ) && !empty( $step_item[$this->prefix . 'step_image_dimension'] )) {
								$dimension = $step_item[$this->prefix . 'step_image_dimension'];

								$steps['image']['height'] = $dimension['height'];
								$steps['image']['width'] = $dimension['width'];
							}
		
						}

						$schema['step'][] = $steps;
					}
				}

				if( isset( $settings[$this->prefix . 'total_time'] ) && !empty( $settings[$this->prefix . 'total_time'] ) ) { 
					$schema['totalTime'] = $settings[$this->prefix . 'total_time'];
				}

				echo '<script type="application/ld+json">'. json_encode($schema) .'</script>';

			}

			/* review schema */
			if( isset($settings[$this->prefix . 'type']) && 'Review' == $settings[$this->prefix . 'type'] ) { 

				$schema = [
					"@context" => "https://schema.org/",
					"@type" => $settings[$this->prefix . 'review_type'],
					"name" => $settings[$this->prefix . 'review_name']
				];

				//echo '<pre>'; print_r($settings[$this->prefix . 'review_images']); echo '</pre>';

				if(!empty($settings[$this->prefix . 'review_images'])) {
					foreach( $settings[$this->prefix . 'review_images'] as $image ) {
						$schema['image'][] = $image['url'];
					}
				}

				if(!empty($settings[$this->prefix . 'brand_name'])) {
					$schema['brand'] = [
						"@type" => "Brand",
						"name" => $settings[$this->prefix . 'brand_name']
					];
				}

				if( isset( $settings[ $this->prefix . 'review_repeater' ] ) && !empty( $settings[ $this->prefix . 'review_repeater' ] ) ) {
					$steps = [];
					
					foreach( $settings[ $this->prefix . 'review_repeater' ] as $review_item ) {
						
						$schema['review'][] = [
							"@type" => "Review",
							"reviewRating" => [
								"@type" => "Rating",
								"ratingValue" => $review_item[$this->prefix . 'rating'],
							],
							"author" => [
								"@type" => "Person",
								"name" => $review_item[$this->prefix . 'author']
							]
						];
					}
				}

				if(!empty($settings[$this->prefix . 'rating_value'])) {
					$schema['aggregateRating']['@type'] = "AggregateRating";
					$schema['aggregateRating']['ratingValue'] = $settings[$this->prefix . 'rating_value'];
				}

				if(!empty($settings[$this->prefix . 'rating_value'])) {
					$schema['aggregateRating']['bestRating'] = $settings[$this->prefix . 'best_rating'];
				}

				if(!empty($settings[$this->prefix . 'rating_value'])) {
					$schema['aggregateRating']['ratingCount'] = $settings[$this->prefix . 'rating_count'];
				}
 								
				if(!empty($settings[$this->prefix . 'offer_low_price']) && !empty($settings[$this->prefix . 'offer_high_price']) && !empty($settings[$this->prefix . 'offer_currency'])) {
					$schema['offers'] = [
						"@type" => "AggregateOffer",
						"lowPrice" => $settings[$this->prefix . 'offer_low_price'],
						"highPrice" => $settings[$this->prefix . 'offer_high_price'],
						"priceCurrency" => $settings[$this->prefix . 'offer_currency']
					];
				}

				echo '<script type="application/ld+json">'. json_encode($schema) .'</script>';
			}
		 }
	}
}
