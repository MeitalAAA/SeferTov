<?php
namespace Gloo\Modules\Relationship_Dynamic_Tags;

Class Jet_Engine_Macro_Tag extends \Elementor\Core\DynamicTags\Tag {

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
		return 'jet-engine-tags';
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
		return __( 'JetEngine Relations', 'gloo_for_elementor' );
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

        $output_option = [
			'type_limeter' => 'Delimeter',
            'type_ul'      => 'Ul Structure',
            'type_ol'      => 'Ol Structure',
			'type_lenght'  => 'Array Length',
			'type_array'   => 'Specific Array',
			'one_per_line'   => 'One Per Line'
        ];

        $return_value = [
			'name'      => 'Name',
			'slug'      => 'Slug',
 			'id' => 'ID',
			'link'  => 'Link'
		];
        
		$this->add_control(
			'otw_relation_type',
			array(
				'label'   => __( 'Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'related_children_from' => __( 'Parent', 'gloo_for_elementor' ),
					'related_parents_from' => __( 'Child', 'gloo_for_elementor' ),
				),
			)
        );

        $get_cpt_args = array(
            'public'   => true,
        );

        $types = [];
        $post_types = get_post_types($get_cpt_args, 'objects');        

        foreach ( $post_types as $type ) {
            $types[$type->name] = $type->label;
        }

        $post_types = get_post_types();
        
        $this->add_control(
			'otw_relation_cpt',
			array(
				'label'   => __( 'Custom Post Type', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $types
			)
        );
        
        $this->add_control(
			'field_output',
			array(
				'label'   => __( 'Output Format', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'delimiter',
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
					'field_output' => 'type_limeter'
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
					'field_output' => 'type_array'
				],
			)
        );
        
        $this->add_control(
			'return_value',
			array(
				'label'   => __( 'Return Value', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'title',
                'options' => $return_value,
                'field_output' => 'type_array',
                'condition' => [
					'field_output' => ['type_limeter','type_ul','type_ol','type_array']
				],
			)
		);

	}


	public function render() {

        $otw_relation_type = $this->get_settings( 'otw_relation_type' );
        $otw_relation_cpt = $this->get_settings( 'otw_relation_cpt' );
        $field_output = $this->get_settings( 'field_output' );
        $delimiter = $this->get_settings( 'delimiter' );
        $array_index = $this->get_settings( 'array_index' );
        $return_value = $this->get_settings( 'return_value' );
				$one_per_line_type = $this->get_settings( 'one_per_line_type' );
        
        $posts = [];

        if(!empty($otw_relation_type)) {

            if($otw_relation_type == 'related_parents_from') {
                
                $posts = jet_engine()->relations->get_related_posts( array(
                    'post_type_1' => $otw_relation_cpt,
                    'post_type_2' => get_post_type(),
                    'from'        => $otw_relation_cpt,
                ) );    

            } else {
                $posts = jet_engine()->relations->get_related_posts( array(
                    'post_type_1' => get_post_type(),
                    'post_type_2' => $otw_relation_cpt,
                    'from'        => $otw_relation_cpt,
                ) );
            }            
           
            if(empty($posts)) {
                return 'not found';
            }

        } else {
            echo 'Select Parent/Child';
        }

        if(!empty($posts)) {            

            if($return_value == 'slug') {

                foreach($posts as $id) {
                    $post = get_post($id); 
                    $post_data[] = urldecode($post->post_name);
                }

            } elseif($return_value == 'id') {
                
                foreach($posts as $id) {
                    $post_data[] = $id;
                }
                
            } elseif($return_value == 'link') {

                foreach($posts as $id) {
                    $post_data[] = get_permalink($id);
                }

            } else {
                foreach($posts as $id) {
                    $post_data[] = get_the_title($id);
                }
            }

            $output = '';

            if(!empty($post_data) && is_array($post_data)) {

                if ( $field_output == 'type_ul' ) {

                    $output .= '<ul class="tax-ul">';

                    foreach ( $post_data as $value ) {
                        $output .= '<li>' . $value . '</li>';
                    }

                    $output .= '</ul>';

                } else if ( $field_output == 'type_ol' ) {

                    $output .= '<ol class="tax-ol">';

                    foreach ( $post_data as $value ) {
                        $output .= '<li>' . $value . '</li>';
                    }

                    $output .= '</ol>';


                } else if ( $field_output == 'type_lenght' ) {

                    $output = count( $post_data );

                } else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

                    $output = implode( $delimiter, $post_data );

                }
								else if ( $field_output == 'one_per_line' ) {
									if($one_per_line_type == 'html')
										$output = implode( '<br />', $post_data );
									else
										$output = implode( PHP_EOL, $post_data );
				
								}

                echo $output;	

            }
       
        }
	}
}