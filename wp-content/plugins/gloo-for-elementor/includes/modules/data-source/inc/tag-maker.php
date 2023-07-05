<?php
namespace Gloo\Modules\Data_Source;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Plugin {

	private static $post_ids = '';


	/**
	 * Instance
	 *
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Plugin An instance of the class.
	 * @access public
	 *
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_menu', [ $this, 'gloo_add_plugin_page' ], 11 );
		// add_action( 'admin_init', [ $this, 'page_init' ] );

		// Register Custom Post Type
		add_action( 'init', [ $this, 'gloo_dynamic_tag_post_type' ], 0 );

		// add meta box to posts
		add_action( 'add_meta_boxes', [ $this, 'dtm_add_meta_boxes' ] );
		add_action('admin_enqueue_scripts', [ $this, 'dtm_meta_box_scripts']);

		// save
		add_action( 'save_post_gloo_dtm', [ $this, 'dtm_save_post' ] );
		add_action( 'admin_notices', [ $this, 'gloo_dtm_admin_notices' ] );

		// ajax sync data
		add_action('wp_ajax_sync_data_source', [ $this, 'sync_data_source']);
		add_action('wp_ajax_nopriv_sync_data_source', [ $this, 'sync_data_source']);

		add_action( 'admin_print_styles-post-new.php',[ $this, 'gloo_dtm_admin_style'], 11 );
		add_action( 'admin_print_styles-post.php', [ $this, 'gloo_dtm_admin_style'], 11 );

		/* cron scheduler for data sheet */
		add_filter( 'cron_schedules', [ $this, 'gloo_googlesheet_schedule' ] );

		// Schedule an action if it's not already scheduled
		if ( ! wp_next_scheduled( 'googlesheet_autosync_action' ) ) {
			wp_schedule_event( time(), 'sync_googlesheet_15_minutes', 'googlesheet_autosync_action' );
		}

		add_action( 'googlesheet_autosync_action', [ $this, 'googlesheet_autosync_action_func'] );
	}

	public function gloo_lsm_redirect_post_location( $location ) {

		if ( 'gloo_dtm' == get_post_type() ) {

			/* Custom code for gloo_lsm post type. */

			if ( isset( $_POST['save'] ) || isset( $_POST['publish'] ) ) {
				return admin_url( "admin.php?page=dynamic_tag_maker" );
			}

		}

		return $location;
	}

	public function gloo_dynamic_tag_post_type() {

		$labels = array(
			'name'           => _x( 'Data Source Dynamic Tag', 'Post Type General Name', 'gloo_for_elementor' ),
			'singular_name'  => _x( 'Data Source Dynamic Tag', 'Post Type Singular Name', 'gloo_for_elementor' ),
			'menu_name'      => __( 'Data Source Dynamic Tag', 'gloo_for_elementor' ),
			'name_admin_bar' => __( 'Data Source Dynamic Tag', 'gloo_for_elementor' ),
		);
		$args   = array(
			'label'               => __( 'Data Source Dynamic Tag', 'gloo_for_elementor' ),
			'description'         => __( 'Data Source Dynamic Tag', 'gloo_for_elementor' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		register_post_type( 'gloo_dtm', $args );

	}

	public function gloo_add_plugin_page() {
		add_submenu_page(
			null, // hide from menu
			'Data Sources Dynamic Tag',
			'Data Sources Dynamic Tag',
			'manage_options',
			'data-source',
			[ $this, 'create_admin_page' ]
		);

		/* google client setting page*/	
		add_submenu_page(
			null, // hide from menu
			'Google Client',
			'Google Client',
			'manage_options',
			'gloo_google_client',
			[ $this, 'google_client_setting_page' ]
		);
	}

	public function dtm_add_meta_boxes() {

		global $post;

		if ( empty( $post ) ) {
			return;
		}

		add_meta_box(
			'dnm_datasource_choice', // $id
			'Data Sources Tag Option', // $title
			[ $this, 'dtm_show_meta_boxes' ], // $callback
			'gloo_dtm',
			'normal' // $context
		);

		add_meta_box(
			'dnm_event_time_choices', // $id
			'Auto Update Setting', // $title
			[ $this, 'dtm_event_timer_boxes' ], // $callback
			'gloo_dtm',
			'normal' // $context
		);

	}

	public function dtm_meta_box_scripts() {
		$screen = get_current_screen();

		if (is_object($screen)) {
			// enqueue only for specific post types
			if (in_array($screen->post_type, ['gloo_dtm'])) {
				// enqueue script
				
				wp_enqueue_script('gloo_dtm_meta_box_script', gloo()->plugin_url( 'assets/js/admin/gloo-dtm.js' ), [ 'jquery' ], gloo()->get_version() );
				wp_localize_script( 'gloo_dtm_meta_box_script', 'data_source_ajax', array( 
					'ajax_url' => admin_url( 'admin-ajax.php')
				));
				
			}
		}

	}

	public function gloo_dtm_admin_style() {
		global $post_type;

		if( 'gloo_dtm' == $post_type ) {
			wp_enqueue_style('meta-boxes-css', gloo()->plugin_url('assets/css/admin/meta-boxes.css'), null, gloo()->get_version());
		}
	}
	

	public function dtm_show_meta_boxes($data) { ?>
		<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table gloo-dtm-metabox">
			<tbody>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="source_type"><?php _e('Source Type', 'gloo_for_elementor'); ?></label>
				</th>
				<td>
					<?php $source_type =  esc_attr( get_post_meta( $data->ID, 'source_type', true ) ); ?>
					<select style="width: 95%" class="code" name="source_type" id="source_type">
						<option value=""><?php _e('-', 'gloo_for_elementor')?></option>
						<option value="google_spreadsheet" <?php echo (!empty($source_type) && $source_type == 'google_spreadsheet')?'selected="selected"':''; ?>><?php _e('Google Spreadsheet', 'gloo_for_elementor')?></option>
					</select>
				</td>
			</tr>
			<tr class="form-field" id="spreadsheet-field-id" <?php if($source_type != 'google_spreadsheet') { echo 'style="display: none;"'; }?>>
				<th valign="top" scope="row">
					<label for="spreadsheet_id"><?php _e('Spreadsheet ID', 'gloo_for_elementor'); ?></label>
				</th>
				<td>
					<?php $spreadsheet_id =  esc_attr( get_post_meta( $data->ID, 'spreadsheet_id', true ) ); ?>
					<input id="spreadsheet_id" name="spreadsheet_id" type="text" style="width: 95%" value="<?php echo $spreadsheet_id;?>" class="code">
				</td>
			</tr>
			<?php $enable_spreadsheet_name =  esc_attr( get_post_meta( $data->ID, 'enable_spreadsheet_name', true ) ); ?>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="spreadsheet_id"><?php _e('Enable Sheet', 'gloo_for_elementor'); ?></label>
					<em><?php _e('Default Sheet1 Enabled'); ?></em>
				</th>
				<td>
				<?php $spreadsheet_id =  esc_attr( get_post_meta( $data->ID, 'spreadsheet_id', true ) ); ?>
					<input type="checkbox" class="flipswitch" id="js-enable-spreadsheet-name" name="enable_spreadsheet_name" <?php echo ( $enable_spreadsheet_name == 'on' ) ? 'checked="checked"' : ''; ?>>
				</td>
			</tr>
			<tr class="form-field" id="js-sheet-name" <?php if($enable_spreadsheet_name != 'on') { echo 'style="display: none;"'; }?>>
				<th valign="top" scope="row">
					<label><?php _e('Sheet Name', 'gloo_for_elementor'); ?></label>
				</th>
				<td>
					<?php $sheet_name =  get_post_meta( $data->ID, 'sheet_name', true ); ?>
					<input name="sheet_name" type="text" placeholder="Sheet1" style="width: 95%" value="<?php echo $sheet_name;?>">
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	public function dtm_event_timer_boxes($source_data) { ?>
		<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table gloo-dtm-metabox">
			<tbody>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="js-gloo-enable-auto-sync"><?php _e('Auto Sync', 'gloo_for_elementor'); ?></label>
				</th>
				<td>
				<?php $auto_sync_enable = get_post_meta( $source_data->ID, 'auto_sync_enable', true ); ?>
					<input type="checkbox" class="flipswitch" id="js-gloo-enable-auto-sync" name="auto_sync_enable" <?php echo ( $auto_sync_enable == '1' ) ? 'checked="checked"' : ''; ?>  value="1">
				</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="source-timer"><?php _e('Auto Scheduler ', 'gloo_for_elementor'); ?></label>
				</th>
				<?php
					$timers = array(
						'minutes' => 'Minutes',
						'hours' =>'Hours'
					);
					$cron_schedule_time =  get_post_meta( $source_data->ID, 'cron_schedule_time', true );
					$cron_schedule_type =  get_post_meta( $source_data->ID, 'cron_schedule_type', true );
				?>
				<td>
 					Auto update every
					<input id="source-timer" name="cron_schedule_time" type="number" placeholder="00" style="width: 20%" value="<?php echo (!empty($cron_schedule_time)) ? $cron_schedule_time : ''; ?>" min="15">

					<select style="width: 20%" class="code" name="cron_schedule_type" id="cron-schedule-type">
						<?php foreach( $timers as $key => $timer ) : ?>
							<option value="<?php echo $key; ?>"<?php echo ($key == $cron_schedule_type)?' selected="selected"':''; ?>><?php echo $timer; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr class="form-field" id="spreadsheet-field-id">
				<th valign="top" scope="row">
					<label><?php _e('Sync Data', 'gloo_for_elementor')?></label>
				</th>
				<td>
					<button id="js-sync-data" class="button button-primary button-large"><?php _e('Sync Now')?> </button>
					<img src="<?php echo gloo()->plugin_url( 'assets/images/admin/ajax-loader.gif'); ?>" class="gloo-loader" style="display: none; width: 20px;" alt="Loading..." />
				</td>
			</tr>
			<?php $cron_date_time = get_post_meta( $source_data->ID, 'cron_date_time', true ); 
			if(!empty($cron_date_time)): ?>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label><?php _e('AutoSync Schedule', 'gloo_for_elementor'); ?></label>
					</th>
					<td>
						<?php 
						$str_date = strtotime($cron_date_time);
						$date = date("Y-m-d", $str_date);
						$time = date("h:i:s A", $str_date);	?>
						<p>Next schedule on <strong><?php echo $date; ?></strong> At <strong><?php echo $time; ?></strong></p>
					</td>
				</tr>
			<?php endif; ?>
			<?php $log = get_post_meta( $source_data->ID, 'last_sync_activity_log', true );
			
			if(!empty($log)) : ?>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label><?php _e('Last AutoSync Log', 'gloo_for_elementor')?></label>
					</th>
					<td>
						<p><span><?php echo $log; ?></span></p>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	<?php }

	public function dtm_save_post() {
		global $post;
		$user_id = get_current_user_id();
		
		// auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if ( isset( $_POST["source_type"] ) ) {
			$value = $_POST["source_type"];
			update_post_meta( $post->ID, 'source_type', $value );
		} else {
			delete_post_meta( $post->ID, 'source_type');
		}

		if ( isset( $_POST["spreadsheet_id"] )  && !empty($_POST["spreadsheet_id"])) {
			$value = $_POST["spreadsheet_id"];
			update_post_meta( $post->ID, 'spreadsheet_id', $value );
		} else {
			delete_post_meta( $post->ID, 'spreadsheet_id');
		}

		if(isset($_POST['enable_spreadsheet_name'])) {
			update_post_meta( $post->ID, 'enable_spreadsheet_name', $_POST['enable_spreadsheet_name'] );
		} else {
			delete_post_meta( $post->ID, 'enable_spreadsheet_name');
		}

		if ( isset( $_POST["sheet_name"] )  && !empty($_POST["sheet_name"])) {
			$sheet_name = $_POST["sheet_name"];
			update_post_meta( $post->ID, 'sheet_name', $sheet_name );
		} else {
			delete_post_meta( $post->ID, 'sheet_name');
		}

		if ( isset( $_POST["cron_schedule_time"] )  && !empty($_POST["cron_schedule_time"])) {
			$schedule_time = $_POST["cron_schedule_time"];
			update_post_meta( $post->ID, 'cron_schedule_time', $schedule_time );
		} else {
			delete_post_meta( $post->ID, 'cron_schedule_time');
		}

		if ( isset( $_POST["cron_schedule_type"] )  && !empty($_POST["cron_schedule_type"])) {
			$schedule_type = $_POST["cron_schedule_type"];
			update_post_meta( $post->ID, 'cron_schedule_type', $schedule_type );
		} else {
			delete_post_meta( $post->ID, 'cron_schedule_type');
		}
		
		if ( (isset( $schedule_time ) && !empty($schedule_time)) && (isset( $schedule_type )  && !empty($schedule_type)) ) {

			$date_string = '+'.$schedule_time.' '.$schedule_type	;
			$datetime = new \DateTime();
			$datetime->format('Y-m-d H:i:s'); // 16 Jan 2020
			$new_time = $datetime->modify($date_string)->format("Y-m-d H:i:s");
 
			update_post_meta( $post->ID, 'cron_date_time', $new_time );
		} else {
			delete_post_meta( $post->ID, 'cron_date_time');
		}

		if ( isset( $_POST["auto_sync_enable"] )  && !empty($_POST["auto_sync_enable"])) {
			$auto_sync_enable = $_POST["auto_sync_enable"];
			update_post_meta( $post->ID, 'auto_sync_enable', $auto_sync_enable );
		} else {
			delete_post_meta( $post->ID, 'auto_sync_enable');
		}

		if (!empty($_POST["spreadsheet_id"]) && !empty($_POST["source_type"]) && $_POST["source_type"] == 'google_spreadsheet') {
 
			$spreadsheetId = $_POST["spreadsheet_id"];
			$spreadsheet_data = $this->get_googlespreadsheet_data($spreadsheetId, $post->ID);
			
			if(!isset($spreadsheet_data->error) && !empty($spreadsheet_data)) {
				$json = wp_json_encode($spreadsheet_data, JSON_UNESCAPED_UNICODE );
				update_post_meta( $post->ID, 'spreadsheet_data', $json );
				
				$message = 'Google spreadsheet data imported successfully and available in dynamic tag';
				
				set_transient("spreadsheet_notice_success_{$post->ID}_{$user_id}", $message, 45);

			} else {
				set_transient("spreadsheet_notice_error_{$post->ID}_{$user_id}", $spreadsheet_data->error->message, 45);
			}
		}			
		
	}

	public function gloo_dtm_admin_notices() {
		
		$screen = get_current_screen();

		if ( $screen->parent_base == 'edit' && !isset($_GET['post_type'])) {
			
			global $post;
			$user_id = get_current_user_id();
			$post_id = $post->ID;
			
			if ( get_transient( "spreadsheet_notice_success_{$post_id}_{$user_id}" ) ) { 
				echo '<div class="notice notice-success is-dismissible"><p>Google spreadsheet data imported successfully and available in dynamic tag</p></div>';
				delete_transient("spreadsheet_notice_success_{$post_id}_{$user_id}");
			}

			if ( $error = get_transient( "spreadsheet_notice_error_{$post_id}_{$user_id}" ) ) { 
				echo '<div class="notice notice-error is-dismissible"><p>'.$error.'</p></div>';
				delete_transient("spreadsheet_notice_error_{$post_id}_{$user_id}");
			}
		}
		
		if ( $error = get_transient( "spreadsheet_notice_credentials_missing" ) ) { 
			echo '<div class="notice notice-error is-dismissible"><p>'.$error.'</p></div>';
			delete_transient("spreadsheet_notice_credentials_missing");
		}

		if ( $notice_client = get_transient( "gloo_client_credentials" ) ) { 
			echo '<div class="notice notice-success is-dismissible"><p>'.$notice_client.'</p></div>';
			delete_transient("gloo_client_credentials");
		}

		if ( array_key_exists('code', $_GET) ) {
		
			switch($_GET['code']) {
				
				case !empty($_GET['code']):
					echo '<div class="notice notice-success is-dismissible"><p>Access token creted and activated succesfully</p></div>';
					break;
			}
		}
	}

	public function get_googlespreadsheet_data($spreadsheet_id, $source_id) {

		if(empty($spreadsheet_id) || empty($source_id)) {
			return;
		}
		
		$gloo_google_access_token = get_option('gloo_google_access_token');
		$gloo_google_key = get_option('gloo_google_key');

		$enable_spreadsheet_name =  get_post_meta( $source_id, 'enable_spreadsheet_name', true );

		if(empty($gloo_google_access_token) || empty($gloo_google_key)) {

			$message = 'Please fill google client credentials from <a href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=gloo_google_client').'">here</a> first ';
			set_transient("spreadsheet_notice_credentials_missing", $message, 45);
			return;
		}

		if($this->CheckTokenExpired($gloo_google_access_token)) {
			$gloo_google_access_token = $this->getRefreshToken($gloo_google_access_token);
			
			if(!isset($gloo_google_access_token->error)) {
				$this->updateToken($gloo_google_access_token);
			}
		}

		$gloo_google_access_token = get_option('gloo_google_access_token');
		
		if(!empty($gloo_google_access_token) && !empty($gloo_google_key)) {
			
			if($enable_spreadsheet_name == 'on') {
				$sheet_name =  get_post_meta( $source_id, 'sheet_name', true );
			} else {
				$sheet_name = 'Sheet1';
			}

 			$access_token = json_decode($gloo_google_access_token);
			$request_uri = 'https://sheets.googleapis.com/v4/spreadsheets/'.$spreadsheet_id.'/values/'.$sheet_name.'!A:ZZZ';
			
			$params = array(
				"key" => $gloo_google_key,
				"access_token" => $access_token->access_token,
				'majorDimension' => 'ROWS'
			);

			$request_url = $request_uri . "?" . http_build_query($params);
 
			try {
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $request_url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
				));

				$response = curl_exec($curl);
				$response = json_decode($response);
				$info = curl_getinfo($curl);
				curl_close($curl);
  
 			} catch(Exception $e) {
				throw new Exception("Invalid URL",0,$e);
			}
						
 			if ($info['http_code'] === 200) {
				return $response->values;
			} else {
				return $response;
			}
		}
	}

	public function google_client_setting_page() {
		
		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); 

		$get_uploaded_json = get_option('gloo_google_credentials');

		if (isset($_GET['code']) && !empty($_GET['code'])) {
			$access_token = $this->generate_access_token_api();

			if(!empty($access_token)) {
				update_option('gloo_google_access_token', json_encode($access_token));
			} 
		}

		if(isset($_POST['save_setting'])) {

			if(!empty($_FILES["google_json"]["name"])) {

				if ( ! is_dir( ABSPATH . 'wp-content/gloo_uploads' ) ) {
					wp_mkdir_p( ABSPATH . 'wp-content/gloo_uploads' );
				}

				/* remove pre uploaded file */
				if(!empty($get_uploaded_json)) {

					$uploaded_path = ABSPATH . 'wp-content/gloo_uploads/' . basename($get_uploaded_json);
					
					if(file_exists($uploaded_path)) {
						unlink(ABSPATH . 'wp-content/gloo_uploads/' . basename($get_uploaded_json));
					}
				}
				
				$get_google_json = $_FILES["google_json"]["name"];				
				$target_file = gloo()->modules_path( 'data-source/').basename($get_google_json);

				$target_file = ABSPATH . 'wp-content/gloo_uploads/' . basename($get_google_json);

				$uploded = move_uploaded_file($_FILES["google_json"]["tmp_name"], $target_file);

				if($uploded) {
					update_option('gloo_google_credentials', $get_google_json);
				}
				
				$message = 'Your credentials saved please create token for authorization';
				set_transient("gloo_client_credentials", $message, 45);
			}

			update_option('gloo_google_key', $_POST['gloo_google_key']);
			
		} ?>
			<div class="wrap">
				<h2><?php _e('Google Client Setting'); ?></h2>

				<form method="post" enctype='multipart/form-data'>
					<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
						<tbody>
							<tr class="form-field">
								<p>Create a project with enabled <strong>Google Sheet API</strong> <a href="https://console.developers.google.com/project" target="_blank">from here </a> and upload json to below field to generate access token</p>	
							</tr>
							<tr class="form-field" id="google-auth">
								<th valign="top" scope="row">
									<label for="google-json"><?php _e('Google Credentials Json', 'gloo_for_elementor')?></label>
								</th>
								<td>
									<input id="google-json" name="google_json" type="file" style="width: 65%" value="<?php ?>" class="code">
									
									<?php if(($get_uploaded_json = get_option('gloo_google_credentials')) && !empty($get_uploaded_json)) :?>
										<p><strong><?php _e('Uploaded File: '); ?></strong> <?php echo $get_uploaded_json; ?></p>
									<?php endif; ?>
								</td>
							</tr>
							<tr class="form-field">
								<th valign="top" scope="row">
									<label for="google-key"><?php _e('API Key', 'gloo_for_elementor')?></label>
								</th>
								<td>
								<?php $gloo_google_key = get_option('gloo_google_key'); ?>
									<input id="google-key" name="gloo_google_key" type="text" style="width: 65%"  value="<?php echo (!empty($gloo_google_key)) ? $gloo_google_key : '';?>">
								</td>
							</tr>
							<tr class="form-field">
								<th valign="top" scope="row">
									<label for="google-json"><?php _e('Set OAuth Redirect Url', 'gloo_for_elementor')?></label>
								</th>
								<td>
									<input id="google-json" name="google_json" type="text" style="width: 65%"  value="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=gloo_google_client');?>" class="code" readonly>
								</td>
							</tr>
							<?php if(($auth_url = $this->get_authorize_url()) && !empty($auth_url)): ?>				
								<tr class="form-field">
									<th>Get Acces Token from Google's OAuth</th>
									<td>
										<a href="<?php echo $auth_url; ?>" target="_blank" class="button button-primary button-large">Get Acces Token </a>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
					<button type="submit" name="save_setting" class="button button-primary button-large"><?php _e('Submit'); ?></button>
				</form>
			</div>
		<?php 
		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' ); 
	}

	public function create_admin_page() {

		include( gloo()->modules_path( 'data-source/inc/dynamic-tag-listing.php' ) );

		global $wpdb;

		$table = new Dynamic_Tag_Listing();
		$table->prepare_items();
	
		$message = '';

		if ('delete' === $table->current_action()) {
			$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'gloo_for_elementor'), count($_REQUEST['id'])) . '</p></div>';
		} 

		$add_new_post = 'post-new.php?post_type=gloo_dtm';

		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-header.php' ); ?>
		
			<div class="wrap">
				<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
				<h2><?php _e('Data Source Dynamic Tag', 'gloo_for_elementor')?> 
					<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), $add_new_post);?>"><?php _e('Add new', 'gloo_for_elementor')?></a>
				</h2>

				<?php echo $message; ?>
			
				<form id="persons-table" method="GET">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<?php $table->display() ?>
				</form>
			</div>
		<?php
		
		include gloo()->plugin_path( 'includes/dashboard/views/common/admin-gloo-footer.php' ); 
	}

	public function get_credentials_data() {
		$get_uploaded_json = get_option('gloo_google_credentials');

		if(!empty($get_uploaded_json)) {

			$credentials_url = ABSPATH . 'wp-content/gloo_uploads/' . basename($get_uploaded_json);

			$json_content = file_get_contents($credentials_url);
			$json_data = json_decode($json_content, true); 

			return $json_data;
		}

		return false;

	}

	public function get_authorize_url() {
		$credentials = $this->get_credentials_data();

		if(!empty($credentials) && isset($credentials['web'])) {
			$auth_data = $credentials['web'];
			$auth_uri = $auth_data['auth_uri'];

			$args_url = array(
				'response_type' => 'code',
				'access_type' => 'offline',
				'client_id' => $auth_data['client_id'],
				'scope' => 'https://www.googleapis.com/auth/spreadsheets.readonly',
				'prompt' => 'select_account consent',
				'redirect_uri' => get_admin_url(get_current_blog_id(), 'admin.php?page=gloo_google_client')
			);

			$auth_url = $auth_uri . "?" . http_build_query($args_url);

			return $auth_url;
		}

	}

	public function generate_access_token_api() {

		$credentials = $this->get_credentials_data();

		if(!empty($credentials) && isset($credentials['web']) && isset($_GET['code']) && !empty($_GET['code'])) {
			$auth_data = $credentials['web'];
			$auth_uri = $auth_data['auth_uri'];

			$code = $_GET['code'];
			$url = 'https://accounts.google.com/o/oauth2/token';
			$params = array(
				"code" => $code,
				"client_id" => $auth_data['client_id'],
				"client_secret" => $auth_data['client_secret'],
				"redirect_uri" => get_admin_url(get_current_blog_id(), 'admin.php?page=gloo_google_client'),
				"grant_type" => "authorization_code",
			);

			$ch = curl_init();
			curl_setopt($ch, constant("CURLOPT_" . 'URL'), $url);
			curl_setopt($ch, constant("CURLOPT_" . 'POST'), true);
			curl_setopt($ch, constant("CURLOPT_" . 'POSTFIELDS'), $params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$output = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);

			$output = json_decode($output);

 			if ($info['http_code'] === 200) {
				return $output;
			} else {
				return false;
			}
		}
	}

	public function getRefreshToken($gloo_google_access_token) {

		$token_details = json_decode($gloo_google_access_token);
		$credentials = $this->get_credentials_data();

		if(empty($credentials) || empty($credentials)) {
			return;
		}

		if(isset($credentials['web'])) {

			$auth_data = $credentials['web'];
			$url = 'https://www.googleapis.com/oauth2/v4/token';

			$params = array(
				"refresh_token" => $token_details->refresh_token,
				"client_id" => $auth_data['client_id'],
				"client_secret" => $auth_data['client_secret'],
				"grant_type" => "refresh_token",
			);

			$ch = curl_init();
			curl_setopt($ch, constant("CURLOPT_" . 'URL'), $url);
			curl_setopt($ch, constant("CURLOPT_" . 'POST'), true);
			curl_setopt($ch, constant("CURLOPT_" . 'POSTFIELDS'), $params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$output = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);
 
			$output = json_decode($output);
 
			return $output;
		}
	}

	public function CheckTokenExpired($token_data) {

		if(empty($token_data)) {
			return;
		}
		
		$token_data = json_decode($token_data);
		$request_uri = 'https://www.googleapis.com/oauth2/v1/tokeninfo';
			
		$params = array(
			"access_token" => $token_data->access_token,
		);
		$request_url = $request_uri . "?" . http_build_query($params);
		
		try {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $request_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
			));

			$response = curl_exec($curl);
			$info = curl_getinfo($curl);
			$output = json_decode($response);
			curl_close($curl);

		} catch(Exception $e) {
			throw new Exception("Invalid URL",0,$e);
		}
 
		if(isset($output->error) && $output->error == 'invalid_token') {
			return true;
		}
	}

	public function updateToken($refreshed_token) {

		if(!empty($refreshed_token)) {
	
			if(isset($refreshed_token->access_token)) {
				
				$token_details = json_decode(get_option('gloo_google_access_token'));				

				$token_details->access_token = $refreshed_token->access_token;
				$token_details->expires_in = $refreshed_token->expires_in;
				
				update_option('gloo_google_access_token', json_encode($token_details));
			}
		}
	}

	public function sync_data_source() {

		$source_id = $_POST['source_id'];

		if(empty($source_id)) {
			return;
		}

		$spreadsheetId = get_post_meta( $source_id, 'spreadsheet_id', true );

		if(!empty($spreadsheetId)) {
			
			$spreadsheet_data = $this->get_googlespreadsheet_data($spreadsheetId, $source_id);

			if(!isset($spreadsheet_data->error) && !empty($spreadsheet_data)) {
				$json = wp_json_encode($spreadsheet_data, JSON_UNESCAPED_UNICODE );
		 
				update_post_meta( $source_id, 'spreadsheet_data', $json);

				$message = 'Spreadsheet data updated successfully';
				$status = true;

			} else { 
				
				$message = $spreadsheet_data->error->message;
				$status = false;			
			}

		} else {
			$message = 'Spreadsheet id required';
			$status = false;			
		}

		wp_send_json(array('message' => $message, 'status' => $status));
	}
	
	function gloo_googlesheet_schedule( $schedules ) {
		$schedules['sync_googlesheet_15_minutes'] = array(
			'interval'  => 900,
			'display'   => __( 'Every 15 Minute', 'otw-dev' )
		);
 
		return $schedules;
	}

	function googlesheet_autosync_action_func() {
		$datetime = new \DateTime();
		$current_date = $datetime->format('Y-m-d H:i:s');
		
		$args = array(
			'post_type' => 'gloo_dtm',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => 'cron_date_time',
					'value'   => current_time( 'mysql' ), // will return something like '2018-03-19 08:23:25'
					'compare' => '<=',
					'type'    => 'DATETIME'
				)
			)
		);

		$the_query = new \WP_Query( $args );
 
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) { 
				$the_query->the_post();
				$source_id = get_the_ID();
 				$auto_sync_enable =  get_post_meta( $source_id, 'auto_sync_enable', true ); 
 
				if($auto_sync_enable == '1') {
					$response = $this->run_auto_sync($source_id);
					if($response) {
						$this->last_sync_activity_log($source_id,$response);
					}
					$this->setup_next_shcedule($source_id);
				}
			}
		}

		wp_reset_postdata();

	}

	public function setup_next_shcedule($source_id) {
		
		$cron_schedule_time = get_post_meta( $source_id, 'cron_schedule_time', true );
		$cron_schedule_type = get_post_meta( $source_id, 'cron_schedule_type', true );
		$cron_date_time = get_post_meta( $source_id, 'cron_date_time', true );
  
 		if ( (isset( $cron_schedule_time ) && !empty($cron_schedule_time)) && (isset( $cron_schedule_type )  && !empty($cron_schedule_type)) ) {

			$date_string = '+'.$cron_schedule_time.' '.$cron_schedule_type;
			$datetime = new \DateTime();
			$datetime->format('Y-m-d H:i:s'); // 16 Jan 2020
			$new_time = $datetime->modify($date_string)->format("Y-m-d H:i:s");
 
			update_post_meta( $source_id, 'cron_date_time', $new_time );
		} 
	}

	public function run_auto_sync($source_id) {

		if(empty($source_id)) {
			return;
		}

		$message = '';
		$spreadsheetId = get_post_meta( $source_id, 'spreadsheet_id', true );

		if(!empty($spreadsheetId)) {
			
			$spreadsheet_data = $this->get_googlespreadsheet_data($spreadsheetId, $source_id);

			if(!isset($spreadsheet_data->error) && !empty($spreadsheet_data)) {
				$json = wp_json_encode($spreadsheet_data, JSON_UNESCAPED_UNICODE );
				update_post_meta( $source_id, 'spreadsheet_data', $json);
				$message = 'Spreadsheet data updated successfully';
 
			} else { 
				$message = $spreadsheet_data->error->message;
 			}

		} else {
			$message = 'Spreadsheet id missing';
 		}

		return $message;
	}

	public function last_sync_activity_log($source_id, $log_message) {
		if(empty($source_id) || empty($log_message)) {
			return;
		}

		$datetime = new \DateTime();
		$log_date = $datetime->format('Y-m-d H:i:s'); // 16 Jan 2020
		$log_message = $log_date.' - '.$log_message;

		update_post_meta( $source_id, 'last_sync_activity_log', $log_message);
	}
}

Plugin::instance();