<?php
namespace Gloo\Modules\ImageCrop;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginDefault extends Plugin{

	private static $instance = null;
	
	/******************************************/
	/***** Single Ton base intialization of our class **********/
	/******************************************/
  public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){
    
		add_action( 'elementor/element/form/section_form_fields/before_section_end', [$this,'add_image_cropping_feature'], 11, 2 );
		add_filter( 'elementor_pro/forms/render/item/upload', [ $this, 'field_render_filter' ], 10, 3 );

		add_action( 'elementor/element/form/section_form_style/after_section_end', [$this, 'add_control_section_to_form'], 10, 2 );

		// add javascript and css to wp-admin dashboard.
		if(is_admin()){
			// 	new Admin\PageSettings();
			// add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}else{
			add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}

		add_action('wp_footer', function(){?>
		<div id="gloo_modal" class="gloo_modal gloo_modal_croppie" style="display:none;">
			<span class="gloo-modal-close cursor" onclick="closeModal()">&times;</span>
			<div class="gloo-modal-content">
				<div id="gloo_cropper_image_container" class="gloo_cropper_image_container">
					<div class="gloo_cropper_image_container_actions"><button class="gloo_cropper_image_container_action_result">Save Changes</button></div>
				</div>
			</div><!-- gloo-modal-content-->
		</div><!-- gloo_modal -->
		<?php });

  }// construct function end here

	public function get_prefix($glue_string = '_'){
		return 'gloo_image_crop'.$glue_string;
	}

	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    if(isset($_GET['page']) && $_GET['page'] === $this->prefix){

      // wp_register_style( $this->prefix.'_wp_admin_css', bbwp_engine()->plugin_url().'modules/db-backup/css/style.css', array(), '1.0.0' );
      // wp_enqueue_style($this->prefix.'_wp_admin_css');

      // wp_register_script( $this->prefix.'_wp_admin_script', bbwp_engine()->plugin_url().'modules/db-backup/js/script.js', array('jquery'), '1.0.0' );
      //wp_enqueue_script( $this->prefix.'_wp_admin_script' );


      //$js_variables = array('prefix' => $this->prefix."_");
      //wp_localize_script( $this->prefix.'_wp_admin_script', $this->prefix, $js_variables );

		}

		if(!is_admin()){
			
			/*wp_register_style( 'cropper_css', 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.0.0-alpha.2/cropper.min.css', array(), '1.0.0' );
      wp_enqueue_style('cropper_css');


			wp_register_script( 'cropper_js', 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.0.0-alpha.2/cropper.min.js', array('jquery'), '1.0.0' );
			wp_enqueue_script( 'cropper_js' );*/
			



			/**************** Cropper jquery ************************/
			// wp_register_style( 'cropper_css', gloo()->plugin_url().'includes/modules/image-crop/assets/frontend/cropper-jquery/cropper.min.css', array(), '1.0.0' );
      // wp_enqueue_style('cropper_css');
			
			// wp_register_script( 'cropper_js', gloo()->plugin_url().'includes/modules/image-crop/assets/frontend/cropper-jquery/cropper.min.js', array('jquery'), '1.0.0' );
			// wp_enqueue_script('cropper_js');

			// wp_register_script( 'cropper_jquery_js', gloo()->plugin_url().'includes/modules/image-crop/assets/frontend/cropper-jquery/jquery-cropper.min.js', array('cropper_js'), '1.0.0' );
			// wp_enqueue_script('cropper_jquery_js');


			/**************** croppie js ************************/
			wp_register_style( 'cropper_css', 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css', array(), '1.0.0' );

			$script_abs_path = gloo()->modules_path( 'image-crop/assets/frontend/css/style.css');
			wp_register_style( $this->get_prefix().'style', gloo()->plugin_url().'includes/modules/image-crop/assets/frontend/css/style.css', array('cropper_css'), get_file_time($script_abs_path));
      // wp_enqueue_style('cropper_css');

			wp_register_script( 'cropper_js', 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js', array('jquery'), '1.0.0' );
			// wp_enqueue_script('cropper_js');

			$script_abs_path = gloo()->modules_path( 'image-crop/assets/frontend/js/script.js');
			wp_register_script( $this->prefix.'_script', gloo()->plugin_url().'includes/modules/image-crop/assets/frontend/js/script.js', array('cropper_js'), get_file_time($script_abs_path));
			// wp_enqueue_script($this->prefix.'_script');
		}

  }// wp_admin_style_scripts

	public function field_render_filter( $item, $item_index, $form ) {

		// $form->add_style_depends( 'gloo-for-elementor' );
		

		if ( isset( $item['gloo_image_crop'] ) && $item['gloo_image_crop'] && $item['gloo_image_crop'] == 'yes') {
			$form->add_script_depends( $this->prefix.'_script' );
			$form->add_style_depends( $this->get_prefix().'style' );
			$form->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo-image-crop-wrapper' );
			$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo-image-crop-input' );
			$crop_options = array(
				'enableResize' => false,
				'button_label' => __('Save changes', 'gloo_for_elementor'),
				'viewport_width' => 350,
				'viewport_height' => 350,
				'viewport_type' => 'square',
				'boundary_width' => 500,
				'boundary_height' => 500,
				'showZoomer' => false,
			);
			
			if(isset($item['gloo_image_crop_is_resize']) && $item['gloo_image_crop_is_resize'] == 'yes')
				$crop_options['enableResize'] = true;
			if(isset($item['gloo_image_crop_showZoomer']) && $item['gloo_image_crop_showZoomer'] == 'yes')
				$crop_options['showZoomer'] = true;
			if(isset($item['gloo_image_crop_button_label']) && $item['gloo_image_crop_button_label'])
				$crop_options['button_label'] = $item['gloo_image_crop_button_label'];
			if(isset($item['gloo_image_crop_type']) && $item['gloo_image_crop_type'])
				$crop_options['viewport_type'] = $item['gloo_image_crop_type'];
			if(isset($item['gloo_image_crop_width']) && isset($item['gloo_image_crop_width']['size']) && $item['gloo_image_crop_width']['size'])
				$crop_options['viewport_width'] = $item['gloo_image_crop_width']['size'];
			if(isset($item['gloo_image_crop_height']) && isset($item['gloo_image_crop_height']['size']) && $item['gloo_image_crop_height']['size'])
				$crop_options['viewport_height'] = $item['gloo_image_crop_height']['size'];

			if(isset($item['gloo_image_crop_boundary_width']) && isset($item['gloo_image_crop_boundary_width']['size']) && $item['gloo_image_crop_boundary_width']['size'])
				$crop_options['boundary_width'] = $item['gloo_image_crop_boundary_width']['size'];
			if(isset($item['gloo_image_crop_boundary_height']) && isset($item['gloo_image_crop_boundary_height']['size']) && $item['gloo_image_crop_boundary_height']['size'])
				$crop_options['boundary_height'] = $item['gloo_image_crop_boundary_height']['size'];

			if(isset($item['gloo_image_crop_upload_size']) && $item['gloo_image_crop_upload_size'])
				$crop_options['image_size'] = $item['gloo_image_crop_upload_size'];

			if(isset($item['gloo_image_crop_upload_size_width']) && $item['gloo_image_crop_upload_size_width'] && isset($item['gloo_image_crop_upload_size_height']) && $item['gloo_image_crop_upload_size_height']){
				$crop_options['image_size_width'] = $item['gloo_image_crop_upload_size_width'];
				$crop_options['image_size_height'] = $item['gloo_image_crop_upload_size_height'];
			}
			
			$form->add_render_attribute( 'input' . $item_index, 'data-crop-options', json_encode($crop_options));
		}

		return $item;
	}

	public function add_image_cropping_feature( $widget, $section_id ) {

		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		if ( isset( $control_data['fields']['gloo_image_crop'] ) ) {
			return;
		}

		$conditions = array(
			'field_type' => 'upload',
		);
		$is_filepond_active = gloo()->modules->is_module_active('gloo_form_filepond_upload');
		if($is_filepond_active)
			$conditions['gloo_filepond_upload!'] = 'yes';

		$tmp = new \Elementor\Repeater();
		$tmp->add_control(
			'gloo_image_crop',
			[
				'name'         => 'gloo_image_crop',
				'label'        => __( 'Enable Image Cropping', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$conditions['gloo_image_crop'] = 'yes';
		$tmp->add_control(
			'gloo_image_crop_note',
			[
				'name'         => 'gloo_image_crop_note',
				// 'type'         => \Elementor\Controls_Manager::SWITCHER,
				'type'         => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Note: It works only for single image.', 'gloo_for_elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
			'gloo_image_crop_button_label',
			[
				'name'         => 'gloo_image_crop_button_label',
				'label'        => __( 'Button Label', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'default' 		 => __('Save changes', 'gloo_for_elementor'),
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
			'gloo_image_crop_is_resize',
			[
				'name'         => 'gloo_image_crop_is_resize',
				'label'        => __( 'Is resize?', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
			'gloo_image_crop_type',
			[
				'name'         => 'gloo_image_crop_type',
				'label'        => __( 'Crop Type', 'gloo' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'square',
				'options' => ['square' => 'Square', 'circle' => 'Circle'],
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
			'gloo_image_crop_showZoomer',
			[
				'name'         => 'gloo_image_crop_showZoomer',
				'label'        => __( 'Show zoomer', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'condition'    => $conditions,
				'default' => 'yes',
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		
		$tmp->add_control(
			'gloo_image_crop_width',
			[
				'name'         => 'gloo_image_crop_width',
				'label'        => __( 'Viewport width', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', /*'%','em','rem','vh' */],
				'default' => [
					'unit' => 'px',
					'size' => '350',
				],
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
			'gloo_image_crop_height',
			[
				'name'         => 'gloo_image_crop_height',
				'label'        => __( 'Viewport height', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', /*'%','em','rem','vh' */],
				'default' => [
					'unit' => 'px',
					'size' => '350',
				],
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);

		$tmp->add_control(
			'gloo_image_crop_boundary_width',
			[
				'name'         => 'gloo_image_crop_boundary_width',
				'label'        => __( 'Boundary width', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', /*'%','em','rem','vh' */],
				'default' => [
					'unit' => 'px',
					'size' => '500',
				],
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
			'gloo_image_crop_boundary_height',
			[
				'name'         => 'gloo_image_crop_boundary_height',
				'label'        => __( 'Boundary height', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'size_units' 	=> [ 'px', /*'%','em','rem','vh' */],
				'default' => [
					'unit' => 'px',
					'size' => '500',
				],
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
			'gloo_image_crop_upload_size',
			[
				'name'         => 'gloo_image_crop_upload_size',
				'label'        => __( 'Image Size', 'gloo' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'viewport',
				'options' => ['viewport' => 'Viewport', 'original' => 'Original', 'custom' => 'Custom'],
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$conditions['gloo_image_crop_upload_size'] = 'custom';
		$tmp->add_control(
			'gloo_image_crop_upload_size_width',
			[
				'name'         => 'gloo_image_crop_upload_size_width',
				'label'        => __( 'Image Size width', 'gloo' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		$tmp->add_control(
			'gloo_image_crop_upload_size_height',
			[
				'name'         => 'gloo_image_crop_upload_size_height',
				'label'        => __( 'Image Size height', 'gloo' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'condition'    => $conditions,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
		);
		
		
		$control_data['fields'] = $this->inject_controls($tmp->get_controls(), $control_data['fields']);
		
		/*$pattern_field = $tmp->get_controls();
		$pattern_field_crop = $pattern_field['gloo_image_crop'];
		$pattern_field_width = $pattern_field['gloo_image_crop_width'];
		
		// insert new class field in advanced tab before field ID control
		$new_order = [];
		foreach ( $control_data['fields'] as $field_key => $field ) {
			if ( 'custom_id' === $field['name'] ) {
				$new_order['field_css_class'] = $pattern_field_crop;
				$new_order['gloo_image_crop_width'] = $pattern_field_width;
			}
			$new_order[ $field_key ] = $field;
		}*/
		// $control_data['fields'] = $new_order;

		// $widget->update_control( 'form_fields', $control_data );

		/*$widget->start_injection( [
			// 'type' => 'section',
			'at' => 'end',
			'of' => $section_id,
		] );
		$widget->add_control(
			'trim_start_dummy',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Trim Start', 'gloo_for_elementor' ),
			]
		);
		$widget->end_injection();*/
		// db($section_id);
		/*$control_data['fields']['gloo_image_crop'] = [
				'name'         => 'gloo_image_crop',
				'label'        => __( 'Enable Image Cropping', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'condition'    => [
					'field_type' => 'upload',
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
		];
		$control_data['fields']['gloo_image_crop_width'] = [
			'name'         => 'gloo_image_crop_width',
			'label'        => __( 'Image Cropping Width', 'gloo' ),
			'type'         => \Elementor\Controls_Manager::SLIDER,
			'range' 		=> [
				'px' 		=> [
					'min' 	=> 0,
					'max' 	=> 1000,
					'step' 	=> 1,
				],
				'%' => [
					'min' 	=> 0,
					'max' 	=> 100,
				],
			],
			'size_units' 	=> [ 'px', '%','em','rem','vh' ],
			'default' => ['unit' => 'px', 'size' => '', 'sizes' => array()],
			'condition'    => [
				'field_type' => 'upload',
			],
			'tab'          => 'content',
			'inner_tab'    => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];*/

		$widget->update_control( 'form_fields', $control_data );
	}
	

	public function inject_controls($repeater_controls, $existing_controls = array()){
		
		if($repeater_controls && is_array($repeater_controls) && count($repeater_controls) >= 1){
			foreach($repeater_controls as $key=>$control){
				$existing_controls[$key] = $control;
			}
		}
		return $existing_controls;
	}


	public function add_control_section_to_form( $element, $args ) {

		// $elementor = \Elementor\Plugin::instance();
		// $control_data = $elementor->controls_manager->get_control_from_stack( $element->get_unique_name(), 'submit_actions' );
		// db($control_data);
		// db($element);exit();
		$element->start_controls_section(
			$this->get_prefix().'style',
			[
				'label' => __( 'Image Cropping UI', 'gloo_for_elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			$this->get_prefix() . 'container_width',
			[
				'label' 		=> __( 'Container Width', 'gloo_for_elementor' ),
				'type' 			=> \Elementor\Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1000,
						'step' 	=> 1,
					],
					'%' => [
						'min' 	=> 0,
						'max' 	=> 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '600',
				],
				'size_units' 	=> [ 'px', '%','em','rem','vh' ],
				'selectors' 	=> [
					'.gloo_modal_croppie .gloo-modal-content' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$element->add_control(
			$this->get_prefix() .'container_padding',
			[
				'label' => __( 'Container Padding', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		// Button controls for every site
		$element->add_control(
			$this->get_prefix() .'heading_submit_button',
			[
				'label' => __( 'Button style', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);
		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => $this->get_prefix() . 'button_typography',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '.gloo_modal_croppie .gloo_cropper_image_container_action_result',
			]
		);
		$element->add_group_control(
			\Elementor\Group_Control_Border::get_type(), [
				'name' => $this->get_prefix() . 'button_border',
				'selector' => '.gloo_modal_croppie .gloo_cropper_image_container_action_result',
				'exclude' => [
					'color',
				],
			]
		);

		$element->start_controls_tabs( $this->get_prefix().'tabs_button_style' );
		$element->start_controls_tab(
			$this->get_prefix().'tab_button_normal',
			[
				'label' => __( 'Normal', 'gloo_for_elementor' ),
			]
		);
		
		$element->add_control(
			$this->get_prefix() .'button_background_color',
			[
				'label' => __( 'Background Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container_action_result' => 'background-color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_text_color',
			[
				'label' => __( 'Text Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				// 'default' => '#ffffff',
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container_action_result' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_border_color',
			[
				'label' => __( 'Border Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container_action_result' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_prefix() .'button_border_border!' => '',
				],
			]
		);
		$element->end_controls_tab();
		$element->start_controls_tab(
			$this->get_prefix().'tab_button_hover',
			[
				'label' => __( 'Hover', 'gloo_for_elementor' ),
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_background_color_hover',
			[
				'label' => __( 'Background Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container_action_result:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_text_color_hover',
			[
				'label' => __( 'Text Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				// 'default' => '#ffffff',
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container_action_result:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_border_color_hover',
			[
				'label' => __( 'Border Color', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container_action_result:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_prefix() .'button_border_border!' => '',
				],
			]
		);
		$element->end_controls_tab();
		$element->end_controls_tabs();
		$element->add_control(
			$this->get_prefix() .'button_border_radius',
			[
				'label' => __( 'Border Radius', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container_action_result' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$element->add_control(
			$this->get_prefix() .'button_text_padding',
			[
				'label' => __( 'Text Padding', 'gloo_for_elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'.gloo_modal_croppie .gloo_cropper_image_container_action_result' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		// $element->add_group_control(
		// 	$this->get_prefix() . 'button_typography',
		// 	// Group_Control_Typography::get_type(),
		// 	[
		// 		'name' => 'button_typography',
		// 		'global' => [
		// 			'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
		// 		],
		// 		'selector' => '{{WRAPPER}} .gloo_modal_croppie .gloo_cropper_image_container_action_result',
		// 	]
		// );


    $element->end_controls_section();
  }

} // BBWP_CustomFields class

