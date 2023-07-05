<?php
namespace Gloo\Modules\Native_Dynamic_Tags_Kit;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'native-dynamic-tags';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->init();
	}


	/**
	 * Init module components
	 *
	 * @return [type] [description]
	 */
	public function init() {
		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_dynamic_tags' ) );
		add_action( 'elementor/frontend/before_render', [ $this, 'before_render_content' ], 10, 1);

		if(is_admin()) {
			add_action( 'elementor/element/before_section_end', [ $this, 'elementor_gallery_widgets_modifier'], 10, 2);	
		}
	}

	public function register_dynamic_tags( $dynamic_tags ) {

			// Include the Dynamic tags
			foreach ( glob( gloo()->modules_path( 'native-dynamic-tags/inc/dynamic-tags/*.php' ) ) as $file ) {
				require $file;
			}	
	
			$classes = [
				'Content_Tag',
				'Plugins_Tag',
				'Post_Type_Count',
				'User_Avtar_Tag',
				'User_Post_Ids',
				'Current_URL',
				'WP_Nonce',
				'Context_Dynamic',
				'User_Role_Dynamic_Tag',
				'Term_Post_Ids'
			];
	
			// register tags
			foreach ( $classes as $class ) {
				$dynamic_tags->register_tag( "Gloo\Modules\Native_Dynamic_Tags_Kit\\{$class}" );
			}

	}
	
	public function elementor_gallery_widgets_modifier($element, $section_id) {
		if($element->get_name() != 'gallery')
			return;
	
		$element->start_injection( [
			'type' => 'section',
			'at' => 'end',
			'of' => $section_id,
		] );
		
		$element->add_control(
			'otw_is_author_images',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __( 'Current User Images', 'gloo_for_elementor' ),
			]
		);
		$element->end_injection();
		
	}
	
  public function before_render_content($widget){

	if($widget->get_name() == 'gallery') {
		if($widget->get_settings('otw_is_author_images') == 'yes') {

			$post_ids = array();

			if(is_author()){
				$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
				$current_user_id = $author->ID;
			} else {
				$current_user_id = get_current_user_id(); 
			}
			
			if($current_user_id) {
				$query_args = array(
					'post_type' => 'attachment',
					'author' => $current_user_id,
					'posts_per_page' => -1,
				);
		
				$posts = get_posts( $query_args);
				
				if($posts){
					foreach($posts as $post){
						$post_ids[] = array('id' => $post->ID, 'url' => wp_get_attachment_url($post->ID));
					}
				}
			}
			
			$widget->set_settings('gallery', $post_ids);
		}
	}
  }

	/**
	 * Returns the instance.
	 *
	 * @return Module
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
