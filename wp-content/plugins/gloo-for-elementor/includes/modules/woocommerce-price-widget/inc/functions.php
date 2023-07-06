<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/******************************************/
/***** Debug functions start from here **********/
/******************************************/
if(!function_exists("otw_woocommerce_price_widget")){
  function otw_woocommerce_price_widget() { 
    return \OTW\WoocommercePriceWidget\WoocommercePriceWidget::instance();
  }
}

/******************************************/
/***** Debug functions start from here **********/
/******************************************/
if(!function_exists("alert")){

  function alert($alertText){
  	echo '<script type="text/javascript">';
  	echo "alert(\"$alertText\");";
  	echo "</script>";
  } // function alert

}// if end


if(!function_exists("db")){

  function db($array1){
  	echo "<pre>";
  	var_dump($array1);
  	echo "</pre>";
	}// function db

}// if



/******************************************/
/***** arrayToSerializeString **********/
/******************************************/
if(!function_exists("ArrayToSerializeString")){
  function ArrayToSerializeString($array){
    if(isset($array) && is_array($array) && count($array) >= 1)
      return serialize($array);
    else
      return serialize(array());
  }
}


/******************************************/
/***** SerializeStringToArray **********/
/******************************************/
if(!function_exists("SerializeStringToArray")){
  function SerializeStringToArray($string){
    if(isset($string) && is_array($string) && count($string) >= 1)
      return $string;
    elseif(isset($string) && $string && @unserialize($string)){
      return unserialize($string);
    }else
      return array();
  }
}




if (!function_exists('otw_price_widget_commonPriceHtml')) {

  function otw_price_widget_commonPriceHtml($price_amt, $regular_price, $sale_price) {
    
    $html_price = '<p class="'.esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ).'">';
      //if product is in sale
      if (($price_amt == $sale_price) && ($sale_price != 0)) {
          $html_price .= '<ins>' . wc_price($sale_price) . '</ins> ';
          $html_price .= '<del>' . wc_price($regular_price) . '</del>';
      }
      //in sale but free
      else if (($price_amt == $sale_price) && ($sale_price == 0)) {
          $html_price .= '<ins>Free!</ins> ';
          $html_price .= '<del>' . wc_price($regular_price) . '</del>';
      }
      //not is sale
      else if (($price_amt == $regular_price) && ($regular_price != 0)) {
          $html_price .= '<ins>' . wc_price($regular_price) . '</ins> ';
      }
      //for free product
      else if (($price_amt == $regular_price) && ($regular_price == 0)) {
          $html_price .= '<ins>Free!</ins> ';
      }
      $html_price .= '</p>';
      return $html_price;
  }

}

if (!function_exists('otw_price_widget_simple_product_price_html')) {
  function otw_price_widget_simple_product_price_html($price, $product) {
    
    if ($product->is_type('simple') || $product->is_type('variation')) {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $price_amt = $product->get_price();
        return otw_price_widget_commonPriceHtml($price_amt, $regular_price, $sale_price);
    }else{
      return $price;
    }

  }
}




if (!function_exists('woo_gloo_price_widget')) {
  function woo_gloo_price_widget($price, $product){
    //db(SerializeStringToArray(get_option('woo_gloo_price_widget')));
    $widget_id = get_option('woo_gloo_price_widget_id');
    if(!$widget_id)
      $widget_id = wp_generate_uuid4();
    $widget_settings = SerializeStringToArray(get_option('woo_gloo_price_widget'));
    $widget_settings['is_archive_loop'] = 'yes';
    
    $otwwcpricewidget = \Elementor\Plugin::instance()->elements_manager->create_element_instance(
      [
        'elType' => 'widget',
        'widgetType' => 'otwwcpricewidget',
        //'id' => wp_generate_uuid4(),
        'id' => $widget_id,
        'settings' => $widget_settings
      ],
      []
    );

    $otwwcpricewidget->enqueue_scripts();
    $otwwcpricewidget->enqueue_styles();
      
    
    
    ob_start(); 
    //$otwwcpricewidget->_print_content();
    //$otwwcpricewidget->print_element();
    //$otwwcpricewidget->render_content();
    /*<div class="elementor-element elementor-element-<?php echo $widget_id; ?> elementor-widget elementor-widget-otwwcpricewidget" data-id="<?php echo $widget_id; ?>" data-element_type="widget" data-widget_type="otwwcpricewidget.default">
		<div class="elementor-widget-container">*/
    
    ?>
          <?php 

            //global $product, $post;
            //db($post->ID);
            /*if(is_product())
              $product = wc_get_product();
            elseif(isset($post) && $post)
              $product = wc_get_product($post->ID);
            
            if ( empty( $product ) ) {
              return;
            }*/

            if($otwwcpricewidget->get_settings( 'toggle_prices_location' ) == 'yes'){

              $display_both = false;
              if($otwwcpricewidget->get_settings( 'display_both_prices' ) == 'yes')
                $display_both = true;

              $price = otw_price_widget_simple_product_price_html($price, $product, $display_both);
              //add_filter('woocommerce_get_price_html', 'otw_price_widget_simple_product_price_html', 100, 2);
            }
      
            $default_variation_price_type = $otwwcpricewidget->get_settings( 'default_variation_price_type' );

            if ( $product->is_type( 'variable' )) {
              
                $lowest_sale_price = wc_price($product->get_variation_price( 'min', true ));
                if($default_variation_price_type == 'high_to_discount'){
                  $new_price = '<del>' . wc_price($product->get_variation_regular_price( 'max', true )) . $product->get_price_suffix() . '</del> – <ins>' . $lowest_sale_price . $product->get_price_suffix() . '</ins>';
                }elseif($default_variation_price_type == 'low_to_discount'){
                  $new_price = '<del>' . wc_price($product->get_variation_regular_price( 'min', true )) . $product->get_price_suffix() . '</del> – <ins>' . $lowest_sale_price . $product->get_price_suffix() . '</ins>';
                }else{
                  $default_attributes = $product->get_default_attributes();
                  foreach($product->get_available_variations() as $variation_values ){
                      foreach($variation_values['attributes'] as $key => $attribute_value ){
                          $attribute_name = str_replace( 'attribute_', '', $key );
                          $default_value = $product->get_variation_default_attribute($attribute_name);
                          if( $default_value == $attribute_value ){
                              $is_default_variation = true;
                          } else {
                              $is_default_variation = false;
                              break; // Stop this loop to start next main lopp
                          }
                      }
                      if( isset($is_default_variation) && $is_default_variation ){
                          $variation_id = $variation_values['variation_id'];
                          break; // Stop the main loop
                      }
                  }

                  // Now we get the default variation data
                  if(isset($is_default_variation) && $is_default_variation && isset($variation_id)){
                      
                      // Get the "default" WC_Product_Variation object to use available methods
                      $default_variation = wc_get_product($variation_id);
                      $regular_price = $default_variation->get_regular_price();
                      $sale_price = $default_variation->get_sale_price();
                      $price_amt = $default_variation->get_price();
                      $new_price = otw_price_widget_commonPriceHtml($price_amt, $regular_price, $sale_price, $display_both);
                      
                  }
                }
                
                if(isset($new_price) /*&& !$product->get_default_attributes() && $default_variation_price_type*/){
                  echo $new_price;
                }else{
                  echo $price;
                }

            }else{
              echo $price;
            }

          /*  </div>
    </div>*/
          ?>
        

    <?php
    $price = ob_get_clean();
    return $price;
  }
}
//add_shortcode( 'woo_gloo_price_widget',  'woo_gloo_price_widget' );
add_action('wp', function(){

  if ( is_post_type_archive( 'product' ) || is_product_category() || is_product_tag() ) {
    add_filter('woocommerce_get_price_html', function($price, $product){
      return woo_gloo_price_widget($price, $product);
    }, 98, 2);
  }
  
 
});

/*if (!function_exists('otw_price_widget_variable_product_price_html')) {
  function otw_price_widget_variable_product_price_html($price, $variation) {
    $variation_id = $variation->variation_id;
    //creating the product object
    $variable_product = new WC_Product($variation_id);

    $regular_price = $variable_product->get_regular_price();
    $sale_price = $variable_product->get_sale_price();
    $price_amt = $variable_product->get_price();

    return otw_price_widget_commonPriceHtml($price_amt, $regular_price, $sale_price);
  }
}

if (!function_exists('otw_price_widget_variable_product_minmax_price_html')) {

  function otw_price_widget_variable_product_minmax_price_html($price, $product) {
    $variation_min_price = $product->get_variation_price('min', true);
    $variation_max_price = $product->get_variation_price('max', true);
    $variation_min_regular_price = $product->get_variation_regular_price('min', true);
    $variation_max_regular_price = $product->get_variation_regular_price('max', true);

    if (($variation_min_price == $variation_min_regular_price) && ($variation_max_price == $variation_max_regular_price)) {
        $html_min_max_price = $price;
    } else {
        $html_price = '<p class="price testprice">';
        $html_price .= wc_price($variation_min_price) . '-' . wc_price($variation_max_price) ;
        //$html_price .= '<del>' . wc_price($variation_min_regular_price) . '-' . wc_price($variation_max_regular_price) . '</del>';
        $html_price .= '</p>';
        $html_min_max_price = $html_price;
    }

    return $html_min_max_price;
  }

}*/