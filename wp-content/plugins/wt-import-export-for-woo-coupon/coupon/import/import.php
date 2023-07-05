<?php

if (!defined('WPINC')) {
    exit;
}

class Wt_Import_Export_For_Woo_Coupon_Import {

    public $post_type = 'shop_coupon';
    public $parent_module = null;
    public $parsed_data = array();
    public $import_columns = array();
    public $merge;
    public $skip_new;
    public $merge_empty_cells;
    public $delete_existing;    
    public $use_sku;
    public $merge_with = 'id';
    public $found_action = 'skip';
    public $id_conflict = 'skip';
    
    // Results
    var $import_results = array();
    
    public $is_coupon_exist = false;

    public function __construct($parent_object) {

        $this->parent_module = $parent_object;
    }

    /* WC object based import  */

    public function prepare_data_to_import($import_data, $form_data,$batch_offset,$is_last_batch) { 
                
        $this->merge_with = !empty($form_data['advanced_form_data']['wt_iew_merge_with']) ? $form_data['advanced_form_data']['wt_iew_merge_with'] : 'id'; 
        $this->found_action = !empty($form_data['advanced_form_data']['wt_iew_found_action']) ? $form_data['advanced_form_data']['wt_iew_found_action'] : 'skip'; 
        $this->id_conflict = !empty($form_data['advanced_form_data']['wt_iew_id_conflict']) ? $form_data['advanced_form_data']['wt_iew_id_conflict'] : 'skip'; 
        $this->merge_empty_cells = isset($form_data['advanced_form_data']['wt_iew_merge_empty_cells']) ? $form_data['advanced_form_data']['wt_iew_merge_empty_cells'] : 0;
        $this->skip_new = isset($form_data['advanced_form_data']['wt_iew_skip_new']) ? $form_data['advanced_form_data']['wt_iew_skip_new'] : 0;  
                
//        $this->merge = !empty($form_data['advanced_form_data']['wt_iew_merge']) ? 1 : 0; 
//        $this->merge_with = !empty($form_data['advanced_form_data']['wt_iew_merge_with']) ? $form_data['advanced_form_data']['wt_iew_merge_with'] : 'id';          
//        $this->found_action = !empty($form_data['advanced_form_data']['wt_iew_merge_with']) ? $form_data['advanced_form_data']['wt_iew_found_action'] : 'import'; 
//        $this->use_same_id = !empty($form_data['advanced_form_data']['wt_iew_use_same_id']) ? 1 : 0;
                
        $this->delete_existing = isset($form_data['advanced_form_data']['wt_iew_delete_existing']) ? $form_data['advanced_form_data']['wt_iew_delete_existing'] : 0;
        $this->use_sku = isset($form_data['advanced_form_data']['wt_iew_use_sku']) ? $form_data['advanced_form_data']['wt_iew_use_sku'] : 0;
         
        Wt_Import_Export_For_Woo_Logwriter::write_log($this->parent_module->module_base, 'import', "Preparing for import.");

        $success = 0;
        $failed = 0;
        $msg = 'Coupon imported successfully.';
        foreach ($import_data as $key => $data) { 
            $row = $batch_offset+$key+1;
            Wt_Import_Export_For_Woo_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Parsing item.");
            $parsed_data = $this->parse_data($data);              
            if (!is_wp_error($parsed_data)){
                Wt_Import_Export_For_Woo_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Processing item.");
                $result = $this->process_item($parsed_data);
                if(!is_wp_error($result)){
                   if($this->is_coupon_exist){
                        $msg = 'Coupon updated successfully.';
                    }
                    $this->import_results[$row] = array('row'=>$row, 'message'=>$msg, 'status'=>true, 'status_msg' => __('Success', 'wt-import-export-for-woo'), 'post_id'=>$result['id'], 'post_link' => Wt_Import_Export_For_Woo_Coupon::get_item_link_by_id($result['id'])); 
                    Wt_Import_Export_For_Woo_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - ".$msg); 
                    $success++;                  
                }else{
                   $this->import_results[$row] = array('row'=>$row, 'message'=>$result->get_error_message(), 'status'=>false, 'status_msg' => __('Failed/Skipped', 'wt-import-export-for-woo'), 'post_id'=>'', 'post_link' => array( 'title' => __( 'Untitled', 'wt-import-export-for-woo' ), 'edit_url' => false ) );
                   Wt_Import_Export_For_Woo_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Processing failed. Reason: ".$result->get_error_message());
                   $failed++;
                }                
            }else{
               $this->import_results[$row] = array('row'=>$row, 'message'=>$parsed_data->get_error_message(), 'status'=>false, 'status_msg' => __('Failed/Skipped', 'wt-import-export-for-woo'), 'post_id'=>'', 'post_link' => array( 'title' => __( 'Untitled', 'wt-import-export-for-woo' ), 'edit_url' => false ) );
               Wt_Import_Export_For_Woo_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Parsing failed. Reason: ".$parsed_data->get_error_message());
               $failed++;                
            }            
        }
        
        if($is_last_batch && $this->delete_existing){
            $this->delete_existing();                        
        } 
        
        $this->clean_after_import();
                
        $import_response=array(
                'total_success'=>$success,
                'total_failed'=>$failed,
                'log_data'=>$this->import_results,
            );
        
        return $import_response;
    }
    
    public function clean_after_import() {
        global $wpdb;
        $posts = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_status = '%s' AND post_type = '%s' ", 'importing' ,$this->post_type)); 
        if($posts){
            array_map('wp_delete_post',$posts);
        }
    }
    
    public function delete_existing() {
    
        $posts = new WP_Query([
            'post_type' => $this->post_type,
            'fields' => 'ids',
            'posts_per_page' => -1,
            'post_status' => array('publish', 'private', 'draft', 'pending', 'future'),
            'meta_query' => [
                [
                    'key' => '_wt_delete_existing',
                    'compare' => 'NOT EXISTS',
                ]
            ]
        ]);
               
        foreach ($posts->posts as $post) {
            $this->import_results['detele_results'][$post] = wp_trash_post($post);
        }
        
        
        $posts = new WP_Query([
            'post_type' => $this->post_type,
            'fields' => 'ids',
            'posts_per_page' => -1,
            'post_status' => array('publish', 'private', 'draft', 'pending', 'future'),
            'meta_query' => [
                [
                    'key' => '_wt_delete_existing',
                    'compare' => 'EXISTS',
                ]
            ]
        ]);        
        foreach ($posts->posts as $post) {
            delete_post_meta($post,'_wt_delete_existing');
        }
                               
    }

    /**
     * Parse the data.
     *
     *
     * @param array $data value.
     *
     * @return array
     */
    public function parse_data($data) {        
        try {
            $data = apply_filters("wt_woocommerce_{$this->parent_module->module_base}_importer_pre_parse_data", $data);

            $mapping_fields = $data['mapping_fields'];
            foreach ($data['meta_mapping_fields'] as $value) {
                $mapping_fields = array_merge($mapping_fields, $value);
            }            
            $item_data = array();
            $default_data = $this->get_default_data();

            $item_data = $default_data;
                        
            if($this->merge && !$this->merge_empty_cells){
                $item_data = array();
            }
            
            $item_data['id'] = $this->wt_parse_id_field($mapping_fields);  
            
            foreach ($mapping_fields as $column => $value) {
                if($this->merge && !$this->merge_empty_cells && $value == ''){
                    continue;
                }               
				$column_og = $column;  // to prevent case change for meta fields
                $column = strtolower($column);
                  
                if ('status' == $column || 'post_status' == $column) {
                    $item_data['status'] = $this->wt_parse_status_field($value);                
                    continue;
                }    

                if ('code' == $column || 'post_title' == $column ) {
                    $item_data['code'] = ($value);
                    continue;
                }   
                
                if ('coupon_amount' == $column) {
                    $item_data['amount'] = (isset($value) && !empty($value) ? wc_format_decimal($value) : '');
                    continue;
                }            
                if ('post_date' == $column) {
                    $item_data['date_created'] = ($value);
                    continue;
                } 
                if ('date_expires' == $column ) {    
                    $offset_base = get_option('gmt_offset', 0);
                    $offset_extra = ($offset_base <= 0) ? " 12:00:00" : " 00:00:00";
                    $coupon_expiry_date = (isset($value) && !empty($value) ? strtotime($value.$offset_extra) : ''); //https://stackoverflow.com/a/23659849/1117368
                    $item_data['date_expires'] = $coupon_expiry_date;
                    continue;
                }
                if ('discount_type' == $column ) {
                    $item_data['discount_type'] = $this->wt_parse_discount_type_field($value);
                    continue;
                }            
                if ('description' == $column || 'post_excerpt' == $column ) {
                    $item_data['description'] = $value;
                    continue;
                }  
                if ('usage_count' == $column ) {
                    $item_data['usage_count'] = $this->wt_parse_int_field($value);
                    continue;
                }
                if ('individual_use' == $column ) {
                    $item_data['individual_use'] = wc_string_to_bool($value);
                    continue;
                }                         
                if ('product_ids' == $column || 'product_SKUs' == $column) {
                    $item_data['product_ids'] = $this->wt_parse_product_ids_field($value,$column);                
                    continue;
                }
                if ('exclude_product_ids' == $column || 'exclude_product_SKUs' == $column ) {
                    $item_data['excluded_product_ids'] = $this->wt_parse_product_ids_field($value,$column);
                    continue;
                }
                if ('usage_limit' == $column ) {
                    $item_data['usage_limit'] = $this->wt_parse_int_field($value);
                    continue;
                }
                if ('usage_limit_per_user' == $column ) {
                    $item_data['usage_limit_per_user'] = $this->wt_parse_int_field($value);
                    continue;
                }
                if ('limit_usage_to_x_items' == $column ) {
                    $item_data['limit_usage_to_x_items'] = ($value);
                    continue;
                }            
                if ('free_shipping' == $column ) {
                    $item_data['free_shipping'] = wc_string_to_bool($value);
                    continue;
                }            
                if ('exclude_sale_items' == $column ) {
                    $item_data['exclude_sale_items'] = wc_string_to_bool($value);                
                    continue;
                }
                if ('product_categories' == $column ) {
                    $item_data['product_categories'] = $this->wt_parse_product_category_field($value);
                    continue;
                }
                if ('exclude_product_categories' == $column) { 
                    $item_data['excluded_product_categories'] = $this->wt_parse_product_category_field($value);                
                    continue;
                }                        
                if ('minimum_amount' == $column ) {
                    $item_data['minimum_amount'] = (isset($value) && !empty($value) ? wc_format_decimal($value) : '');
                    continue;
                }
                if ('maximum_amount' == $column) {
                    $item_data['maximum_amount'] = (isset($value) && !empty($value) ? wc_format_decimal($value) : '');
                    continue;
                }
                if ('customer_email' == $column ) {
                    $item_data['email_restrictions'] = $this->wt_explode_values($value,',');
                    continue;
                }  
				if ('wt_iew_coupon_categories' == $column ) {
                    $item_data['other_taxomomy_data'][] = $this->wt_parse_other_taxomomy_field($value);
                    continue;
                } 
                if (strstr($column, 'meta:')) {
                    $item_data['meta_data'][] = $this->wt_parse_meta_field($value, $column_og);
                    continue;
                }
            }      
			
            return $item_data;
        

			
        } catch (Exception $e) {
            return new WP_Error('woocommerce_product_importer_error', $e->getMessage(), array('status' => $e->getCode()));
        }

    }

    /**
     * Explode CSV cell values using commas by default, and handling escaped
     * separators.
     *
     * @since  3.2.0
     * @param  string $value     Value to explode.
     * @param  string $separator Separator separating each value. Defaults to comma.
     * @return array
     */
    protected function wt_explode_values($value, $separator = ',') {
        
        if($value){
            $value = str_replace('\\,', '::separator::', $value);
            $values = explode($separator, $value);
            $values = array_map(array($this, 'wt_explode_values_formatter'), $values);
			 return $values;
        }

    }

    /**
     * Remove formatting and trim each value.
     *
     * @since  3.2.0
     * @param  string $value Value to format.
     * @return string
     */
    protected function wt_explode_values_formatter($value) {
        return trim(str_replace('::separator::', ',', $value));
    }
    
    
    /**
    * Parse discount type.
    *
    * @since 3.0.0
    * @param string $discount_type Discount type.
    */
    public function wt_parse_discount_type_field( $discount_type ) {
        if ( 'percent_product' === $discount_type ) {
                $discount_type = 'percent'; // Backwards compatibility.
        }
        if ( ! in_array( $discount_type, array_keys( wc_get_coupon_types() ), true ) ) {
                //$this->error( 'coupon_invalid_discount_type', __( 'Invalid coupon discount type', 'woocommerce' ) );
                throw new Exception(sprintf('Invalid coupon discount type. Type: %s',$discount_type ));
        }

        return $discount_type;
           
    }

    public function wt_parse_product_ids_field($value, $column) {
		
        $product_ids =array();
		$value_separator = ',';
		if (strpos($value, '|') !== false) {
			$value_separator = '|';
		}
		if($this->use_sku && !empty($value)){
            $prod_skus = explode($value_separator,$value );
            foreach ($prod_skus as $sku_val){
              $product_ids[] = wc_get_product_id_by_sku($sku_val);
            }   
            
        }else{           
            if(!empty($value)){
                $product_ids =  explode($value_separator,$value );
            }                        
        }

        return $product_ids;
    }

    public function wt_parse_type_field($value) {

        $value = array_map('strtolower', $value);
        if (!$value) {
            $value = $this->post_type;
        }

        return $value;
    }

    public function wt_parse_meta_field($value, $column) {
        $meta_key = trim(str_replace('meta:', '', $column));
		$coupon_serialized_metas = apply_filters('wt_iew_coupon_serialized_metas', array('meta:wt_order_Status_need_to_count'));
		if(in_array($column, $coupon_serialized_metas)){
			$value = maybe_serialize( json_decode( $value ) );
		}
        return array('key' => $meta_key, 'value' => $value);
    }

    public function wt_parse_email_field($value) {
        return is_email($value) ? $value : '';
    }

	
	 /**
	 * handle extra/other (custom taxonomy by other plugin) taxonomies.
	  * For WebToffee smart coupon pro - shop_coupon_cat is additional taxonomy 
	 */ 
        public function wt_parse_other_taxomomy_field($value, $column='shop_coupon_cat') {
            // Get taxonomy
            $taxonomy = trim(str_replace('tax:', '', $column));

            // Exists?
            if (!taxonomy_exists($taxonomy)) {
                return;
            }

            if (empty($value)) {
                return array();
            }

            $names = explode( ",", $value);
            $taxs = array();

            foreach ($names as $name) {
                $parent = null;
                $other_terms = array_map( 'trim', explode( '>', $name ) );
                $total  = count( $other_terms );

                foreach ( $other_terms as $index => $_term ) {
                        $term = term_exists( $_term, $taxonomy, $parent );
                        if ( is_array( $term ) ) {
                                $term_id = $term['term_id'];                             
                        }else {
                                $term = wp_insert_term( $_term, $taxonomy, array( 'parent' => intval( $parent ) ) );
                                if ( is_wp_error( $term ) ) {
                                        break; 
                                }
                                $term_id = $term['term_id'];
                        }
                        if ( ( 1 + $index ) === $total ) {
                                $taxs[] = $term_id;
                        } else {
                                $parent = $term_id;
                        }
                }
            }
            return array('taxonomy' => $taxonomy, 'terms' => $taxs);
        }
		
	function set_other_taxonomy_data($data) {
        
        if (isset($data['other_taxomomy_data']) && !empty($data['other_taxomomy_data'])) {
             $terms_to_set = array();

            foreach ($data['other_taxomomy_data'] as $term_group) {

                $taxonomy = $term_group['taxonomy'];
                $terms = $term_group['terms'];
				
                if (!$taxonomy || !taxonomy_exists($taxonomy)) {
                    continue;
                }

                if (!is_array($terms)) {
                    $terms = array($terms);
                }

                $terms_to_set[$taxonomy] = array();

                foreach ($terms as $term_id) {

                    $terms_to_set[$taxonomy][] = intval($term_id);
                }
            }

            foreach ($terms_to_set as $tax => $ids) {
                if(isset($data['id'])&& !empty($data['id'])){
                    wp_set_post_terms($data['id'], $ids, $tax, false);
                }
            }

            unset($data['other_taxomomy_data'], $terms_to_set);

        }
    }	
		
		
	/**
     * Parse category names to IDs.
     *
     * @param string $value Field value.
     *
     * @return array
     */
    public function wt_parse_product_category_field($product_categories) {
        if (empty($product_categories)) {
            return array();
        }
        $cpn_product_categories = explode(',', $product_categories);
        $cpn_product_category_ids = array();
        foreach ($cpn_product_categories as $cpn_product_category_name) {
            
            $cpn_product_category_obj = get_term_by( 'name', $cpn_product_category_name, 'product_cat' );
            $cpn_product_category_ids[] = $cpn_product_category_obj->term_id;
            
        }
        
        return $cpn_product_category_ids;
    }  
    
    /**
     * Parse relative field and return ID.
     * 
     * Handles `id` and Coupon code.
     *
     * If we're not doing an update, create a post and return ID
     * for rows following this one.
     *
     * @param array $data  mapped data.
     *
     * @return int|Exception
     */
    public function wt_parse_id_field($data ) {
        global $wpdb; 
        $coupon_id = 0;
        $this->is_coupon_exist = false;
        
        $id = isset($data['ID']) && !empty($data['ID']) ? absint($data['ID']) : 0;         
        $id_found_with_id = '';
        if($id && 'id' == $this->merge_with ){                    
            $id_found_with_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' ) AND ID = %d;", $id)); // WPCS: db call ok, cache ok.
            if($id_found_with_id){
               if($this->post_type == get_post_type($id_found_with_id)){
                   $this->is_coupon_exist = true;
                   $coupon_id = $id_found_with_id;
               }
            }            
        }                

        $code = isset($data['post_title']) ? $data['post_title'] : '';
        $id_found_with_code = '';
        if(!empty($code && 'code' == $this->merge_with)){            
            $post_db_type = $this->post_type;
            $post_pass_type = '"'.$post_db_type.'"';
            $db_query = $wpdb->prepare("
                            SELECT $wpdb->posts.ID
                            FROM $wpdb->posts
                            WHERE $wpdb->posts.post_type = $post_pass_type
                            AND $wpdb->posts.post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )
                            AND $wpdb->posts.post_title = '%s'
                         ", $code);
            $id_found_with_code = $wpdb->get_var($db_query); 
            $this->is_coupon_exist = true;
            $coupon_id = $id_found_with_code;
        }
        
        if($this->is_coupon_exist){
            if('skip' == $this->found_action){
                if($id && $id_found_with_id ){
                    throw new Exception(sprintf('Coupon with same ID already exists. ID: %d',$id ));
                }elseif($code && $id_found_with_code ){
                    throw new Exception(sprintf('%s with same Code already exists. Code: %s',ucfirst($this->parent_module->module_base),$code ));
                }else{
                    throw new Exception('Coupon already exists.');
                }                 
            }elseif('update' == $this->found_action){
                $this->merge = true; 
                return $coupon_id;
            }                            
        }
        
        if($this->skip_new){
            throw new Exception('Skipping new item' );
        } 
        
        if($id && $id_found_with_id && !$this->is_coupon_exist && 'skip' == $this->id_conflict){
            throw new Exception(sprintf('Importing Coupon(ID) conflicts with an existing post. ID: %d',$id ));
        }
        
        if(empty($code)){
            throw new Exception(sprintf('Cannot insert without %s Code', ucfirst($this->parent_module->module_base)) );
        }
                            
        $postdata = array( // if not specifiying id (id is empty) or if not found by given id or coupon 
            'post_title'      => $code,
            'post_status'    => 'importing',
            'post_type'      => $this->post_type,
        );                
        if(isset($id) && !empty($id)){
            $postdata['import_id'] = $id;
        }                   
        $post_id = wp_insert_post( $postdata, true );                
        if($post_id && !is_wp_error($post_id)){
            Wt_Import_Export_For_Woo_Logwriter::write_log($this->parent_module->module_base, 'import', sprintf('Importing as new '. ($this->parent_module->module_base).' ID:%d',$post_id ));
            return $post_id;
        }else{
            throw new Exception($post_id->get_error_message());
        }

    }       
    

    /**
     * Parse relative comma-delineated field and return product ID.
     *
     * @param string $value Field value.
     *
     * @return array
     */
    public function wt_parse_relative_comma_field($value) {
        if (empty($value)) {
            return array();
        }

        return array_filter(array_map(array($this, 'wt_parse_relative_field'), $this->wt_explode_values($value)));
    }

    /**
     * Parse a comma-delineated field from a CSV.
     *
     * @param string $value Field value.
     *
     * @return array
     */
    public function parse_comma_field($value) {
        if (empty($value) && '0' !== $value) {
            return array();
        }

        $value = $this->unescape_data($value);
        return array_map('wc_clean', $this->wt_explode_values($value));
    }

    /**
     * Parse a field that is generally '1' or '0' but can be something else.
     *
     * @param string $value Field value.
     *
     * @return bool|string
     */
    public function wt_parse_bool_field($value) {
        if ('0' === $value) {
            return false;
        }

        if ('1' === $value) {
            return true;
        }

        // Don't return explicit true or false for empty fields or values like 'notify'.
        return wc_clean($value);
    }

   
    /**
     * Parse download file urls, we should allow shortcodes here.
     *
     * Allow shortcodes if present, othersiwe esc_url the value.
     *
     * @param string $value Field value.
     *
     * @return string
     */
    public function wt_parse_download_file_field($value) {
        // Absolute file paths.
        if (0 === strpos($value, 'http')) {
            return esc_url_raw($value);
        }
        // Relative and shortcode paths.
        return wc_clean($value);
    }

    /**
     * Parse an int value field
     *
     * @param int $value field value.
     *
     * @return int
     */
    public function wt_parse_int_field($value) {
        // Remove the ' prepended to fields that start with - if needed.
//		$value = $this->unescape_data( $value );

        return intval($value);
    }

    /**
     * Parse the published field. 1 is published, 0 is private, -1 is draft.
     * Alternatively, 'true' can be used for published and 'false' for draft.
     *
     * @param string $value Field value.
     *
     * @return float|string
     */
    public function wt_parse_status_field($value) {
        
        $post_status = array('publish', 'private', 'draft', 'pending', 'future', 'trash' );

        $found_status = false;

        foreach ($post_status as $status_name) {
            if (0 == strcasecmp($status_name, $value))
                $found_status = true;
        }

        if ($found_status) {
            return $value;
        }else{
            return 'draft';
        }

    }
        

    public function get_default_data() {

        return  array(
//                'id' => 0,
		'code'                        => '',
		'amount'                      => 0,
		'date_created'                => null,
		'date_modified'               => null,
		'date_expires'                => null,
		'discount_type'               => 'fixed_cart',
		'description'                 => '',
		'usage_count'                 => 0,
		'individual_use'              => false,
		'product_ids'                 => array(),
		'excluded_product_ids'        => array(),
		'usage_limit'                 => 0,
		'usage_limit_per_user'        => 0,
		'limit_usage_to_x_items'      => null,
		'free_shipping'               => false,
		'product_categories'          => array(),
		'excluded_product_categories' => array(),
		'exclude_sale_items'          => false,
		'minimum_amount'              => '',
		'maximum_amount'              => '',
		'email_restrictions'          => array(),
		'used_by'                     => array(),
		'virtual'                     => false,
	);
    }

    public function process_item($data) {

        try {
            do_action('wt_woocommerce_coupon_import_before_process_item', $data);
            $data = apply_filters('wt_woocommerce_coupon_import_process_item', $data);  
                        
            $post_id = $data['id'];
            $object = new WC_Coupon($post_id);
 
//            $object = $this->get_object($data);

            if (is_wp_error($object)) {
                return $object;
            }
            
            Wt_Import_Export_For_Woo_Logwriter::write_log($this->parent_module->module_base, 'import', "Found coupon object. ID:".$object->get_id());            

            $boolean_keys = apply_filters( 'wt_ier_coupon_boolean_keys', array( 'exclude_sale_items', 'individual_use', 'free_shipping', 'date_expires' ) );
            

			if (isset($data['other_taxomomy_data'])) {
                    $this->set_other_taxonomy_data($data);  
					unset( $data['other_taxonomy_data'] );
            } 
			
            foreach ($data as $key => $value) {
                
                if(in_array($key, $boolean_keys)){
                    $fn ='set_'.$key;
                    if(method_exists($object,'set_'.$key)){
                        $object->$fn($value);
                    }
                    continue;
                }
                
                if(!empty($value)){
                    $fn ='set_'.$key;
                    if(method_exists($object,'set_'.$key)){
                        $object->$fn($value);
//                        unset($data[$key]);
                    }
                }

            }
                        
            $this->set_meta_data($object, $data);            

            $update_post = array(
                'ID' => $post_id,
                'post_status' => $data['status'],
            );
            wp_update_post($update_post);
            
            if($this->delete_existing){
                update_post_meta($post_id, '_wt_delete_existing', 1);
            }
            
            $object = apply_filters('wt_woocommerce_import_pre_insert_coupon_object', $object, $data);
            $object->save();

            do_action('wt_woocommerce_import_inserted_coupon_object', $object, $data);

            $result = array(
                'id' => $object->get_id(),
                'updated' => $this->merge,
            );
            return $result;
        } catch (Exception $e) {
            return new WP_Error('woocommerce_product_importer_error', $e->getMessage(), array('status' => $e->getCode()));
        }
    }
    function get_object($data) {
        $id = isset($data['id']) ? absint($data['id']) : 0;

        // Type is the most important part here because we need to be using the correct class and methods.
        if (isset($data['type'])) {
            $types = array_keys(wc_get_product_types());
            $types[] = 'variation';

            if (!in_array($data['type'], $types, true)) {
                return new WP_Error('woocommerce_product_importer_invalid_type', __('Invalid product type.', 'woocommerce'), array('status' => 401));
            }

            try {
                // Prevent getting "variation_invalid_id" error message from Variation Data Store.
                if ('variation' === $data['type']) {
                    $id = wp_update_post(
                            array(
                                'ID' => $id,
                                'post_type' => 'product_variation',
                            )
                    );
                }

                $product = wc_get_product_object($data['type'], $id);
            } catch (WC_Data_Exception $e) {
                return new WP_Error('woocommerce_product_csv_importer_' . $e->getErrorCode(), $e->getMessage(), array('status' => 401));
            }
        } elseif (!empty($data['id'])) {
            $product = wc_get_product($id);

            if (!$product) {
                return new WP_Error(
                        'woocommerce_product_csv_importer_invalid_id',
                        /* translators: %d: product ID */ sprintf(__('Invalid product ID %d.', 'woocommerce'), $id), array(
                    'id' => $id,
                    'status' => 401,
                        )
                );
            }
        } else {
            $product = wc_get_product_object('simple', $id);
        }

        return apply_filters('wt_woocommerce_order_import_get_product_object', $product, $data);
    }

    public function set_meta_data( &$object, $data ) {
		if ( isset( $data[ 'meta_data' ] ) ) {
			$final_order_meta_string = '';
			$coupon_id				 = $object->get_id();
			foreach ( $data[ 'meta_data' ] as $meta ) {
				if ( !$this->merge_empty_cells && empty( $meta[ 'value' ] ) ) {
					continue;
				}
				$meta_key	 = $meta[ 'key' ];
				$meta_value	 = $meta[ 'value' ];
				if ( 'skip' == $this->found_action ) {
					$final_order_meta_string .= "('$coupon_id', '$meta_key', '$meta_value'),";
				} else {
					$object->update_meta_data( $meta[ 'key' ], $meta[ 'value' ] );
				}
			}
			if ( 'skip' == $this->found_action ) {
				global $wpdb;
				$final_order_meta_string = rtrim( $final_order_meta_string, "," );
				if ( $final_order_meta_string != '' )
					$wpdb->query( "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) VALUES $final_order_meta_string" );
			}
		}
	}

}
