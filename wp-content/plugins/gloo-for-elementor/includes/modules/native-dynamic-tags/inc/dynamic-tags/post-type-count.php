<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

Class Post_Type_Count extends \Elementor\Core\DynamicTags\Tag {

	/**
	* Get Name
	*
	* Returns the Name of the tag
	*
	* @since 2.0.0
	* @access public
	*
	* @return string
	*/
	public function get_name() {
		return 'post-type-count';
	}

	/**
	* Get Title
	*
	* Returns the title of the Tag
	*
	* @since 2.0.0
	* @access public
	*
	* @return string
	*/
	public function get_title() {
		return __( 'Post Type Count', 'gloo_for_elementor' );
	}
   
	/**
	* Get Group
	*
	* Returns the Group of the tag
	*
	* @since 2.0.0
	* @access public
	*
	* @return string
	*/
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	* Get Categories
	*
	* Returns an array of tag categories
	*
	* @since 2.0.0
	* @access public
	*
	* @return array
	*/
	public function get_categories() {
		return [
			\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY
		];
	}

	/**
	* Register Controls
	*
	* Registers the Dynamic tag controls
	*
	* @return void
	* @since 2.0.0
	* @access protected
	*
	*/
	protected function _register_controls() {

		$post_types = array();
		$types = get_post_types( [], 'objects' );

		foreach ( $types as $type ) {
			$post_types[$type->name] = $type->label;
		}

		$labels = [];
		$tax_args = [
			'public' => true,
		];

		$taxonomies = get_taxonomies($tax_args);
		$field_sources = [];
		
		if(!empty($taxonomies)) {
			foreach ($taxonomies as $tax) {
				$tax_info = get_taxonomy($tax);
				$labels[$tax] = $tax_info->label;
				
				$terms = get_terms( array(
					'taxonomy' => $tax_info->name,
					'hide_empty' => false,
				) );
				
				foreach( $terms as $term ) {
					$field_sources[$term->term_id] = $term->name;
				}
			}
		}

		$this->add_control(
			'term_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => array(
					'current_term' => __( 'Current Term', 'gloo_for_elementor' ),
					'custom_term' => __( 'Custom Term', 'gloo_for_elementor' ),
				),
			)
		);
		
		$this->add_control(	
			'count_post_types',
			array(
				'label'   => __( 'Post Types', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => '',
				'multiple' => true,
				'options' => $post_types,
			)
		);

		$this->add_control(
			'count_taxonomies',
			array(
				'label'   => __( 'Taxonomy', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => '',
				'multiple' => true,
				'options' => $field_sources,	
				'condition' => [
					'term_context!' => 'current_term',
				],
			)
		);

		$return_value = [
			'count' => 'Count',
			'id' => 'ID'
		];

		$this->add_control(
			'return_value',
			array(
				'label'   => __( 'Return Value', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'count',
				'options' => $return_value,
			)
		);

		$output_option = [
			'type_ul'      => 'Ul Structure',
			'type_ol'      => 'Ol Structure',
			'type_limeter' => 'Delimeter',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
		];
    
		$this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'type_none',
				'options' => $output_option,
			)
		);

		$this->add_control(
			'one_per_line_type',
			array(
				'label'     => __( 'Line Break Type', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default' => 'php',
				'options' => array('php' => 'PhP', 'html' => 'HTML'),
				'condition' => [
					'field_output' => 'one_per_line'
				],
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'field_output' => 'type_limeter',
					'return_value' => 'id'
				],
			)
		);

		$this->add_control(
			'array_index',
			array(
				'label'     => __( 'Array Index', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'condition' => [
					'field_output' => 'type_array',
					'return_value' => 'id'
				],
			)
		);

	}


	public function render() {
		$term_context = $this->get_settings( 'term_context' );
		$return_value = $this->get_settings( 'return_value' );
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$array_index = $this->get_settings( 'array_index' );
		$post_types = $this->get_settings( 'count_post_types' );
		$taxonomies = $this->get_settings( 'count_taxonomies' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		$tax_query = [];

		if( $term_context  == 'custom_term' ) {

			if( !empty($taxonomies) ) {
				$tax_query['relation'] = 'AND';
			
				foreach( $taxonomies as $term ) {
					$term_obj = get_term($term);
	
					$tax_query['tax_query'][] = array(
						'taxonomy' => $term_obj->taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_obj->term_id
					);
				}
			}
			
		} else {
			$current_object = get_queried_object();

			if( !empty($current_object ) ) {

				$class = get_class( $current_object );

				if ( 'WP_Term' === $class ) {
					$tax_query['tax_query'][] = array(
						'taxonomy' => $current_object->taxonomy,
						'field'    => 'term_id',
						'terms'    => $current_object->term_id
					);
				} else {

					return false;
					
				}
			}
		}

		if( !empty($post_types) ) {

			$args = array(
				'post_type' => $post_types,
				'posts_per_page' => -1,
				'tax_query' => $tax_query
			);

			$query = new \WP_Query($args);
			$data = [];

			if($return_value == 'count') {
				
				$data = $query->found_posts;

			} elseif($return_value == 'id') {
			
				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) : $query->the_post();
						$data[] = get_the_ID();
					endwhile;
				endif;
					// Reset Post Data
				wp_reset_postdata();
			}
			
			$output = '';

			if(!empty($data)) {

				if ( $field_output == 'type_ul' && !is_array($data)) {

					$output .= '<ul class="tax-ul">';
					$output .= '<li>' . $data . '</li>';
					$output .= '</ul>';

				} else if ( $field_output == 'type_ol' && !is_array($data)) {

					$output .= '<ol class="tax-ol">';					
					$output .= '<li>' . $data . '</li>';
					$output .= '</ol>';
				} else if ( $field_output == 'type_lenght' && is_array($data)) {

					$output = count( $data );

				} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) && is_array($data)) {
					
					$output = implode( $delimiter, $data );

				} else if ( $field_output == 'type_array' && is_numeric($array_index) && is_array($data)) {

					if ( isset( $data[ $array_index ] ) && ! empty( $data[ $array_index ] ) ) {
						$output = $data[ $array_index ];
					}
				}else if ( $field_output == 'one_per_line' ) {
					if($one_per_line_type == 'html')
						$output = implode( '<br />', $data );
					else
						$output = implode( PHP_EOL, $data );

				}

				echo $output;

			}
		}
	}
	
}