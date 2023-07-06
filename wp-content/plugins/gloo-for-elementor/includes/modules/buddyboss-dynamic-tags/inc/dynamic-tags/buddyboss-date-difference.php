<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class Date_Difference extends \Elementor\Core\DynamicTags\Tag {

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
		return 'elementor-date-difference';
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
		return __( 'Difference Between Dates', 'gloo_for_elementor' );
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
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
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

		$wordpress_date_format = get_option('date_format');

		$this->add_control(
			'date_difference_start',
			array(
				'label'       => __( 'Start Date', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'Current Date',
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'date_difference_start_format',
			array(
				'label'       => __( 'Start Date Format', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => 'The format we should expect, to use your default WordPress date format use: ' . $wordpress_date_format,
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'date_difference_end',
			array(
				'label'       => __( 'End Date', 'gloo_for_elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'Current Date',
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'date_difference_end_format',
			array(
				'label'       => __( 'End Date Format', 'gloo_for_elementor' ),
				'description' => 'The format we should expect, to use your default WordPress date format use: ' . $wordpress_date_format,
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
			)
		);


		$default_date_options           = $this->get_default_date_options();
		$default_date_options['custom'] = 'Custom';
		$this->add_control(
			'date_difference_output',
			array(
				'label'   => __( 'Output', 'gloo_for_elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $default_date_options
			)
		);

		$this->add_control(
			'date_difference_output_custom',
			array(
				'label'       => __( 'Custom Output', 'gloo_for_elementor' ),
				'description' => 'Usable Variables: <br>%seconds%, %minutes% ,%hours% ,%days% ,%months% ,%years% <br>Example: %years% Year(s) and %months% Month(s)',
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'condition'   => [
					'date_difference_output' => 'custom'
				],
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'date_difference_handle_negative',
			array(
				'label'        => __( 'Always Positive Value', 'gloo_for_elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'description'  => 'Convert the value to a positive value',
				'label_on'     => __( 'Yes', 'your-plugin' ),
				'label_off'    => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

	}

	public function render() {

		$settings = $this->get_settings_for_display();

		// values
		$start = $this->is_unix_timestamp( $settings['date_difference_start'] ) ? '@' . $settings['date_difference_start'] : $settings['date_difference_start'];
		$end   = $this->is_unix_timestamp( $settings['date_difference_end'] ) ? '@' . $settings['date_difference_end'] : $settings['date_difference_end'];

		// no input
		if ( ! $start && ! $end ) {
			return;
		}

		// format
		$start_format = $settings['date_difference_start_format'];
		$end_format   = $settings['date_difference_end_format'];

		// output
		$output          = $settings['date_difference_output'];
		$always_positive = $settings['date_difference_handle_negative'];

		$custom_output        = $output === "custom" ? $settings['date_difference_output_custom'] : '';
		$default_date_options = $this->get_default_date_options();

		try {
			$start_datetime = $start_format && $start ? \DateTime::createFromFormat( $start_format, $start ) : new \DateTime( $start );
			$end_datetime   = $end_format && $end ? \DateTime::createFromFormat( $end_format, $end ) : new \DateTime( $end );

			if ( ! $start_datetime instanceof \DateTime || ! $end_datetime instanceof \DateTime ) {
				return;
			}

			$difference = $start_datetime->diff( $end_datetime );
			if ( isset( $default_date_options[ $output ] ) ) { // default output
				$value = $this->get_total_interval( $difference, $output );
				if ( $always_positive === "yes" && $value ) {
					$value = abs( $value );
				}
			} else { //custom output
				$get_default_date_placeholders = $this->get_default_date_placeholders();
				preg_match_all( '/%([a-zA-Z])*%/', $custom_output, $custom_format_output );
				for ( $i = 0; $i < sizeof( $custom_format_output[0] ); $i ++ ) {
					$custom_var = $custom_format_output[0][ $i ];
					if ( isset( $get_default_date_placeholders[ $custom_var ] ) ) {
						$custom_output = str_replace( $custom_var, $difference->{$get_default_date_placeholders[ $custom_var ]}, $custom_output );
					}
				}
				$value = $custom_output;
			}
		} catch ( \Exception $e ) {
			return;
		}

		echo $value;

	}

	public function is_unix_timestamp( $timestamp ) {
		return ( (string) (int) $timestamp === $timestamp )
		       && ( $timestamp <= PHP_INT_MAX )
		       && ( $timestamp >= ~PHP_INT_MAX )
		       && ( ! strtotime( $timestamp ) );
	}

	public function get_default_date_options() {
		return [
			'seconds' => 'Seconds',
			'minutes' => 'Minutes',
			'hours'   => 'Hours',
			'days'    => 'Days',
			'weeks'   => 'Weeks',
			'months'  => 'Months',
			'years'   => 'Years',
		];
	}


	public function get_default_date_placeholders() {
		return [
			'%seconds%' => 's',
			'%minutes%' => 'i',
			'%hours%'   => 'h',
			'%days%'    => 'd',
			'%months%'  => 'm',
			'%years%'   => 'y',
		];
	}

	public function get_total_interval( $interval, $type ) {
		$sign = $interval->format( '%r' );
		switch ( $type ) {
			case 'years':
				return $sign . $interval->format( '%Y' );
				break;
			case 'months':
				$years  = $interval->format( '%Y' );
				$months = 0;
				if ( $years ) {
					$months += $years * 12;
				}
				$months += $interval->format( '%m' );

				return $sign . $months;
				break;
			case 'weeks':
				$weeks = 0;
				$days  = $interval->format( '%a' );
				if ( $days >= 7 ) {
					$weeks = floor( $days / 7 );
				}

				return $sign . $weeks;
				break;
			case 'days':
				return $sign . $interval->format( '%a' );
				break;
			case 'hours':
				$days  = $interval->format( '%a' );
				$hours = 0;
				if ( $days ) {
					$hours += 24 * $days;
				}
				$hours += $interval->format( '%H' );

				return $sign . $hours;
				break;
			case 'minutes':
				$days    = $interval->format( '%a' );
				$minutes = 0;
				if ( $days ) {
					$minutes += 24 * 60 * $days;
				}
				$hours = $interval->format( '%H' );
				if ( $hours ) {
					$minutes += 60 * $hours;
				}
				$minutes += $interval->format( '%i' );

				return $sign . $minutes;
				break;
			case 'seconds':
				$days    = $interval->format( '%a' );
				$seconds = 0;
				if ( $days ) {
					$seconds += 24 * 60 * 60 * $days;
				}
				$hours = $interval->format( '%H' );
				if ( $hours ) {
					$seconds += 60 * 60 * $hours;
				}
				$minutes = $interval->format( '%i' );
				if ( $minutes ) {
					$seconds += 60 * $minutes;
				}
				$seconds += $interval->format( '%s' );

				return $sign . $seconds;
				break;
			default:
				return null;
		}
	}
}
