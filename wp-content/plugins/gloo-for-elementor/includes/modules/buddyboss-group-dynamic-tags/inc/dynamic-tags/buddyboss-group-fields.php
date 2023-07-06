<?php
namespace Gloo\Modules\BB_Group_Dynamic_Tags;

class BuddyBoss_Group_Fields extends \Elementor\Core\DynamicTags\Tag {

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
		return 'buddyboss-group-fields';
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
		return __( 'BuddyBoss Group Fields', 'gloo_for_elementor' );
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
			'member_ids' => 'Members ID\'s',
			'amt_of_member' => 'Amount of Memebers',
			'group_type' => 'Group Type',
			'admins_ids' => 'Admin ID\'s',
			'moderator_ids' => 'Moderator ID\'s',
			'all_members' => 'All Members ID\'s'
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
			$group_id =& $groups_template->group->id;
		}

		if( !empty($group_id) ) {
			$args = [
				'group_id' => $group_id
			];

			$members = groups_get_group_members($args);

			if( $item_output == 'member_ids' ) {
				if( isset($members['members']) && !empty($members['members']) ) {
					foreach( $members['members'] as $member ) {
						$data[] = $member->ID;
					}
				}
			} else if( $item_output == 'amt_of_member' ) {
				if( isset($members['count']) && !empty($members['count']) ) {
					$data[] = groups_get_total_member_count($group_id);
				}
			} else if( $item_output == 'group_type' ) { 
				//$types = bp_groups_get_group_type($group_id);
				$group        = groups_get_group( $group_id );
				
				$data[] = $group->status;
			} else if ( $item_output == 'admins_ids' ) {

				$admins = groups_get_group_admins($group_id);

				if(!empty($admins)) {
					foreach($admins as $admin) {
						$data[] = $admin->user_id;
					}
				}

			} else if( $item_output == 'moderator_ids' ) {
				$moderator_ids  = groups_get_group_mods($group_id);

				if(!empty($moderator_ids)) {
					foreach($moderator_ids as $moderator) {
						$data[] = $moderator->user_id;
					}
				}
			} else if( $item_output == 'all_members' ) {
				$args = [
					'group_id' => $group_id,
					'exclude_admins_mods' => false
				];
	
				$members = groups_get_group_members($args);

				if( isset($members['members']) && !empty($members['members']) ) {
					foreach( $members['members'] as $member ) {
						$data[] = $member->ID;
					}
				}
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