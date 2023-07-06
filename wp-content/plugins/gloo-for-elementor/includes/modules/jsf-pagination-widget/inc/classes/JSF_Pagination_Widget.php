<?php

namespace Gloo\JSFPaginationWidget;

use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class JSF_Pagination_Widget extends \Elementor\Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		wp_register_script( 'gloo-pagination', gloo()->plugin_url('includes/modules/').'jsf-pagination-widget/assets/js/script.js', [ 'elementor-frontend', 'jquery' ], '1.0.0', true );
		wp_register_style( 'gloo-pagination-css', gloo()->plugin_url('includes/modules/').'jsf-pagination-widget/assets/css/gloo-pagination-style.css' );
 }

	public function get_name() {
		return 'gloo-jsf-pagination';
	}

	public function get_title() {
		return __( 'Gloo JSF Pagination', 'gloo_for_elementor' );
	}

  
  
	public function get_icon() {
		return 'gloo-elements-icon-power';
		//return 'gloo-elements-icon-composer';
		//return 'jet-smart-filters-icon-pagination';
	}


	public function get_categories() {
		return [ 'gloo' ];
	}

	public function get_script_depends() {
		return [ 'gloo-pagination' ];
	}

	public function get_style_depends() {
		return [ 'gloo-pagination-css' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'gloo_for_elementor' ),
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'Pagination for:', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array('' => '--Select--', 'jet-engine' => 'JetEngine'),
			)
		);

		$this->add_control(
			'epro_posts_notice',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => __( 'Please set <b>gloo-pagination</b> into Query ID option of Posts widget you want to filter', 'gloo_for_elementor' ),
				'condition' => array(
					'content_provider' => array( 'epro-posts', 'epro-portfolio' ),
				),
			)
		);

		$this->add_control(
			'apply_type',
			array(
				'label'   => __( 'Apply type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'reload',
				'options' => array(
					//'ajax'   => __( 'AJAX', 'gloo' ),
					'reload' => __( 'Page reload', 'gloo_for_elementor' ),
					//'mixed'  => __( 'Mixed', 'gloo' ),
				),
			)
		);
		$this->add_control(
      'gloo_keep_query_arg',
      [
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label' => __( 'Display Query Args in URL', 'gloo_for_elementor' ),
      ]
    );
		
		$this->add_control(
			'query_id',
			array(
				'label'       => esc_html__( 'Query ID', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'gloo_for_elementor'),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_controls',
			array(
				'label' => __( 'Controls', 'gloo_for_elementor'),
			)
		);

		$this->add_control(
			'enable_prev_next',
			array(
				'label'        => esc_html__( 'Enable Prev/Next buttons', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => '',
				'label_on'     => esc_html__( 'Yes', 'gloo_for_elementor' ),
				'label_off'    => esc_html__( 'No', 'gloo_for_elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prev_text',
			array(
				'label'   => esc_html__( 'Prev Text', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Prev', 'gloo_for_elementor' ),
			)
		);

		$this->add_control(
			'next_text',
			array(
				'label'   => esc_html__( 'Next Text', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Next', 'gloo_for_elementor' ),
			)
		);

		$this->add_control(
			'pages_center_offset',
			array(
				'label'   => esc_html__( 'Items center offset', 'gloo_for_elementor' ),
				'description'   => esc_html__( 'Set number of items to either side of current page, not including current page.Set 0 to show all items.', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 50,
				'step'    => 1,
			)
		);

		$this->add_control(
			'pages_end_offset',
			array(
				'label'   => esc_html__( 'Items edge offset', 'gloo_for_elementor' ),
				'description'   => esc_html__( 'Set number of items on either the start and the end list edges.', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 50,
				'step'    => 1,
			)
		);

		$this->add_control(
			'provider_top_offset',
			array(
				'label'       => esc_html__( 'Provider top offset', 'gloo_for_elementor' ),
				'description' => esc_html__( 'Set the distance from the top edge when reloading the content via AJAX.', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 300,
				'step'        => 1,
				'condition'   => array(
					'apply_type' => array( 'ajax', 'mixed' ),
				),
				'render_type'  => 'none'
			)
		);

		$this->end_controls_section();

		$css_scheme = apply_filters(
			'gloo-pagination/css-scheme',
			array(
				'pagination'              => '.gloo-pagination',
				'pagination-item'         => '.gloo-pagination__item',
				'pagination-link'         => '.gloo-pagination__link',
				'pagination-link-current' => '.gloo-pagination__current .gloo-pagination__link',
				'pagination-dots'         => '.gloo-pagination__dots',
			)
		);
		$this->controls_section_pagination( $css_scheme );

	}

	protected function controls_section_pagination( $css_scheme ) {

		$this->start_controls_section(
			'gloo_pagination_style',
			array(
				'label'      => esc_html__( 'Gloo Pagination', 'gloo_for_elementor' ),
				'tab'        => \Elementor\Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);
		$this->add_control(
			'pagination_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_border',
				'label'       => esc_html__( 'Border', 'gloo_for_elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['pagination'],
			)
		);
		$this->add_control(
			'pagination_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pagination_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['pagination'],
			)
		);
		$this->add_responsive_control(
			'pagination_padding',
			array(
				'label'      => esc_html__( 'Padding', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['pagination'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'pagination_margin',
			array(
				'label'      => esc_html__( 'Margin', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'pagination_items_style',
			array(
				'label'      => esc_html__( 'Items', 'gloo_for_elementor' ),
				'tab'        => \Elementor\Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_items_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['pagination-link'] . ', {{WRAPPER}} ' . $css_scheme['pagination-dots'],
			)
		);
		$this->start_controls_tabs( 'tabs_pagination_items_style' );
		$this->start_controls_tab(
			'pagination_items_normal',
			array(
				'label' => esc_html__( 'Normal', 'gloo_for_elementor' ),
			)
		);
		$this->add_control(
			'pagination_items_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_color',
			array(
				'label'     => esc_html__( 'Text Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'pagination_items_hover',
			array(
				'label' => esc_html__( 'Hover', 'gloo_for_elementor' ),
			)
		);
		$this->add_control(
			'pagination_items_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'pagination_items_active',
			array(
				'label' => esc_html__( 'Current', 'gloo_for_elementor' ),
			)
		);
		$this->add_control(
			'pagination_items_bg_color_active',
			array(
				'label'     => esc_html__( 'Background Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_active_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link-current'] => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'pagination_items_dots',
			array(
				'label' => esc_html__( 'Dots', 'gloo_for_elementor' ),
			)
		);
		$this->add_control(
			'pagination_items_bg_color_dots',
			array(
				'label'     => esc_html__( 'Background Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_color_dots',
			array(
				'label'     => esc_html__( 'Text Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'pagination_items_dots_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => array(
					'pagination_items_border_border!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'pagination_items_padding',
			array(
				'label'      => esc_html__( 'Padding', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'pagination_items_horizontal_gap',
			array(
				'label'       => esc_html__( 'Horizontal Gap Between Items', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 4,
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-item'] . '+' . $css_scheme['pagination-item'] => 'margin-left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_items_vertical_gap',
			array(
				'label'       => esc_html__( 'Vertical Gap Between Items', 'gloo_for_elementor' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 4,
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-item'] => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_items_border',
				'label'       => esc_html__( 'Border', 'gloo_for_elementor' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['pagination-link'] . ', {{WRAPPER}} ' . $css_scheme['pagination-dots'],
			)
		);
		$this->add_responsive_control(
			'pagination_items_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-link'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['pagination-dots'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'pagination_items_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'gloo_for_elementor' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'gloo_for_elementor' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'gloo_for_elementor' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination'] => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  [type] $el [description]
	 *
	 * @return [type]     [description]
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	protected function render() {

		$base_class       = $this->get_name();
		$settings         = $this->get_settings();
		$content_provider = $settings['content_provider'];
		$apply_type       = $settings['apply_type'];
		$query_id         = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$controls_enabled = isset( $settings['enable_prev_next'] ) ? $settings['enable_prev_next'] : '';

		if($query_id != 'default'){
			$active_pagination_ids = SerializeStringToArray(get_option('active_pagination_ids'));
			$active_pagination_ids[$query_id] = $query_id;
			update_option('active_pagination_ids', ArrayToSerializeString($active_pagination_ids));
		}

		if ( 'yes' === $controls_enabled ) {

			$controls = array(
				'nav'  => true,
				'prev' => $settings['prev_text'],
				'next' => $settings['next_text'],
			);

		} else {
			$controls['nav'] = false;
		}

		$controls['pages_mid_size']      = ! empty( $settings['pages_center_offset'] ) ? absint( $settings['pages_center_offset'] ) : 0;
		$controls['pages_end_size']      = ! empty( $settings['pages_end_offset'] ) ? absint( $settings['pages_end_offset'] ) : 0;
		$controls['provider_top_offset'] = ! empty( $settings['provider_top_offset'] ) ? absint( $settings['provider_top_offset'] ) : 0;

		$page_url = $this->get_page_url();
		$permalink_structure = 'yes';
		if('' === get_option('permalink_structure'))
			$permalink_structure = 'no';
		
		$keep_query_arg = ! empty( $settings['gloo_keep_query_arg'] ) ? 'yes' : 'no';
		
		printf(
			'<div
				class="%1$s"
				data-apply-provider="%2$s"
				data-content-provider="%2$s"
				data-query-id="%3$s"
				data-controls="%4$s"
				data-apply-type="%5$s"
				data-page-url="%6$s"
				data-page-url-arg="%7$s"
				data-permalink-structure="%8$s"
				data-keep-query-arg="%9$s"
			>',
			$base_class."_each",
			$content_provider,
			$query_id,
			htmlspecialchars( json_encode( $controls ) ),
			$apply_type,
			$page_url['url'],
			$page_url['arg'],
			$permalink_structure,
			$keep_query_arg
		);

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			$this->render_pagination_sample( $controls );
		}
		
		echo '</div>';

	}


	/**
	 * Render pagination sample
	 *
	 * @return [type] [description]
	 */
	public function render_pagination_sample( $controls ) {

		$pages          = 10;
		$page           = 4;
		$nav            = filter_var( $controls['nav'], FILTER_VALIDATE_BOOLEAN );
		$pages_mid_size = $controls['pages_mid_size'];
		$pages_end_size = $controls['pages_end_size'];
		$pages_show_all = ( 0 === $pages_mid_size ) ? true : false;
		$dots           = true;
		$item_html      = '<div class="gloo-pagination__link">%s</div>';
		//$item_html = '<div class="gloo-pagination__link"><% $value %></div>';
		$dots_html      = '<div class="gloo-pagination__dots">&hellip;</div>';
		
		echo '<div class="gloo-pagination">';
			if ( $nav ) {
				
				echo '<div class="gloo-pagination__item prev-next prev">';
					$value = $controls['prev'];
					/*eval( '?>' . $item_html . '<?php ' );*/
					//echo '<div class="gloo-pagination__link">'.$value.'</div>';
					echo sprintf($item_html, $value);
				echo '</div>';
			}
			for ( $i = 1; $i <= $pages ; $i++ ) {
				$current = ( $page === $i ) ? ' gloo-pagination__current' : '';
				$show_dots =  ( $pages_end_size < $i && $i < $page - $pages_mid_size ) || ( $pages_end_size <= ( $pages - $i ) && $i > $page + $pages_mid_size );

				if ( !$show_dots || $pages_show_all ) {
					$dots = true;
					echo '<div class="gloo-pagination__item' . $current . '">';
						$value = $i;
						/*eval( '?>' . $item_html . '<?php ' );*/
						//echo '<div class="gloo-pagination__link">'.$value.'</div>';
						echo sprintf($item_html, $value);
					echo '</div>';
				} elseif ( $dots ) {
					$dots = false;
					echo '<div class="gloo-pagination__item">';
						eval( '?>' . $dots_html . '<?php ' );
					echo '</div>';
				}
			}
			if ( $nav ) {
				echo '<div class="gloo-pagination__item prev-next next">';
					$value = $controls['next'];
					/*eval( '?>' . $item_html . '<?php ' );*/
					//echo '<div class="gloo-pagination__link">'.$value.'</div>';
					echo sprintf($item_html, $value);
				echo '</div>';
			}
		echo '</div>';
	}


	 /**
	 * Get Page URL
	 *
	 * @return [url]
	 */
	public function get_page_url() {
		global $wp;
		$current_url = array();
		$current_url['arg'] = add_query_arg(array($_GET), '');
		if('' === get_option('permalink_structure')){
			$current_url['url'] = home_url($wp->request);
			//$current_url = home_url(add_query_arg(array($_GET), $wp->request));
		}
		else{
			$current_url['url'] = home_url($wp->request);
			$current_url_array = explode('/page', $current_url['url']);
			$current_url['url'] = trailingslashit($current_url_array[0]);
		}
		
		return ($current_url);
	}

}
