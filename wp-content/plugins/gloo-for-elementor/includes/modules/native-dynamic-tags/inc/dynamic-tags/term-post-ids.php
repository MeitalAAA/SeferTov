<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

class Term_Post_Ids extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'term-post-ids';
	}

	/**
	 * Get Title
	 *
	 * Returns the title of the Tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Current Term Post Ids', 'gloo_for_elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_group() {
		return 'gloo-dynamic-tags';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @return array
	 * @since 2.0.0
	 * @access public
	 *
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

		$this->add_control(
			'term_context',
			array(
				'label'   => __( 'Context', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'queried_term',
				'options' => array(
					'queried_term' => __( 'Queried Term', 'gloo_for_elementor' ),
				),
			)
		);

		$post_types = array();
		$types = get_post_types( [], 'objects' );

		foreach ( $types as $type ) {
			$post_types[$type->name] = $type->label;
		}

		/* post types */
		$this->add_control(	
			'post_type',
			array(
				'label'   => __( 'Post Types', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'default' => 'post',
				'options' => $post_types,
			)
		);

		$this->add_control(
			'post_meta_key',
			array(
				'label'     => __( 'Post Meta Key', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'post_meta_value',
			array(
				'label'     => __( 'Post Meta value', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label'     => __( 'Delimiter', 'gloo_for_elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default' => ',',
			)
		);

	}

	public function render() {

		$post_ids  = [];
		$settings  = $this->get_settings_for_display();
		$post_meta_key = $settings['post_meta_key'];
		$post_meta_value = $settings['post_meta_value'];
		$post_type = $settings['post_type'];
		$delimiter = $settings['delimiter'];

		if(function_exists('jet_engine')){

			$object = jet_engine()->listings->data->get_current_object();
				
			if( !empty( $object ) && isset($object->term_id) && isset($object->count) && $object->count >= 1) {

					if(!empty($post_type) && isset($object->taxonomy)){
						$query_args = array(
							'post_type' => $post_type,
							'posts_per_page' => -1,
							'tax_query' => array(
								array(
									'taxonomy' => $object->taxonomy,
									'field'    => 'term_id',
									'terms'    => $object->term_id,
								),
							),
						);
						if(!empty($post_meta_key))
							$query_args['meta_key'] = $post_meta_key;
						if(!empty($post_meta_value))
							$query_args['meta_value'] = $post_meta_value;

						$posts = get_posts( $query_args);
						if($posts){
							foreach($posts as $post){
								$post_ids[] = $post->ID;
							}
						}
					}
				
			}

			if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
				$default_delimiter = ',';
				if(!empty($delimiter))
					$default_delimiter = $delimiter;
				echo implode( $delimiter, $post_ids );
			}

		}

	}
}