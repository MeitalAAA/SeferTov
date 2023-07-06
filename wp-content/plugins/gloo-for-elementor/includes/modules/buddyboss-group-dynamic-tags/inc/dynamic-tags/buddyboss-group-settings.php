<?php
namespace Gloo\Modules\BB_Group_Dynamic_Tags;

class BuddyBos_Group_Settings extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddyboss-group-settings';
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
		return __( 'BuddyBoss Group Settings', 'gloo_for_elementor' );
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
			'group_query',
			array(
				'label'   => __( 'Group', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_group',
				'options' => [
					'current_group' => 'Current Group',
					'queried_group' => 'Queried Group'
				],
			)
		);

		$item_options = array(
			'group_privacy' => 'Group Privacy',
			'invite_status' => 'Invite Status',
			'feed_status' => 'Feed Status',
			'album_status' => 'Album Status',
			'media_status' => 'Media Status',
			'message_status' => 'Message Status'
		);

		$this->add_control(
			'item_output',
			array(
				'label'   => __( 'Items Output', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $item_options,
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
				'default' => '',
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
	}

	public function render() {
		$group_query = $this->get_settings( 'group_query' );
		$queried_group_id = $this->get_settings( 'queried_group_id' );
		$field_output = $this->get_settings( 'field_output' );
		$delimiter = $this->get_settings( 'delimiter' );
		$array_index = $this->get_settings( 'array_index' );
		$item_output = $this->get_settings( 'item_output' );
		$one_per_line_type = $this->get_settings( 'one_per_line_type' );

		$group_id = bp_get_current_group_id();

 		if( $group_query == 'queried_group' ) {
			global $groups_template;
			if(is_object($groups_template) && property_exists($groups_template, 'group') && $groups_template->group && property_exists($groups_template->group, 'id')){
				$group_id =& $groups_template->group->id;
			}
		}

		if( !empty($group_id) ) {
	
			if( $item_output == 'group_privacy' ) {
				
				$group = groups_get_group( $group_id );
				$data[] = $group->status;
				
			} else if( $item_output == 'invite_status' ) {
				$data[] = bp_group_get_invite_status( $group_id );
			} else if( $item_output == 'feed_status' ) { 
				$data[] = bp_group_get_activity_feed_status( $group_id );
			} else if ( $item_output == 'album_status' ) {
				$data[] = bp_group_get_album_status( $group_id );
			} else if( $item_output == 'media_status' ) {
				$data[] = bp_group_get_media_status( $group_id );
			} else if( $item_output == 'message_status' ) {
				$data[] = bp_group_get_message_status( $group_id );
			}
		}

		$output = '';

		if(!empty($data) && is_array($data)) {

			if ( $field_output == 'type_ul' ) {

				$output .= '<ul class="tax-ul">';

				foreach ( $data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ul>';

			} else if ( $field_output == 'type_ol' ) {

				$output .= '<ol class="tax-ol">';

				foreach ( $data as $value ) {
					$output .= '<li>' . $value . '</li>';
				}

				$output .= '</ol>';


			} else if ( $field_output == 'type_lenght' ) {

				$output = count( $data );

			} else if ( $field_output == 'type_limeter' && ! empty( $delimiter ) ) {

				$output = implode( $delimiter, $data );

			} else if ( $field_output == 'type_array' && is_numeric($array_index) ) {

				if ( isset( $data[ $array_index ] ) && ! empty( $data[ $array_index ] ) ) {
					$output = $data[ $array_index ];
				}

			}
			else if ( $field_output == 'one_per_line' ) {
				if($one_per_line_type == 'html')
					$output = implode( '<br />', $data );
				else
					$output = implode( PHP_EOL, $data );

			}

			echo $output;

		}
	 
	}
}