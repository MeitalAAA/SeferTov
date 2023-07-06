if (typeof numberWithCommas != 'function') {
  function numberWithCommas(x, seperator = ',') {
    return x.toLocaleString('en-US').replaceAll(",", seperator);
    // return x.toString().toLocaleString('en-US').replaceAll(",", seperator);
    // return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, seperator);
  }
}
jQuery( document ).ready(function($) {
  if($(".gloo_composer_math_text").length >= 1){
    
    $(".gloo_composer_math_text").each(function(){

      gloo_calculated_variables_values = $(this).data("calculated-variables-values");
      gloo_calculated_variables_count = $(this).data("calculated-variables-count");
      gloo_calculated_variables_exist = $(this).data("calculated-variables-exist");
      if(gloo_calculated_variables_count == gloo_calculated_variables_exist){
        gloo_calculated_text = $(this).data('calculated-text');
        gloo_calculated_text = new Function("return " + gloo_calculated_text)();

        gloo_composer_math_calculated_variables = $(this).data("calculated-variables");
        gloo_composer_math_calculated_new_value = gloo_calculated_text;
        if(gloo_composer_math_calculated_variables.composer_math_decimal == 'yes' && gloo_composer_math_calculated_variables.composer_math_decimal_amount >= 0){
          gloo_composer_math_calculated_new_value = gloo_composer_math_calculated_new_value.toFixed(gloo_composer_math_calculated_variables.composer_math_decimal_amount);
        }
    
        if(gloo_composer_math_calculated_variables.composer_math_thousand_seperator_switch == 'yes' && gloo_composer_math_calculated_variables.composer_math_thousand_seperator_value != ''){
          gloo_composer_math_calculated_new_value = numberWithCommas(gloo_composer_math_calculated_new_value, gloo_composer_math_calculated_variables.composer_math_thousand_seperator_value);
        }
    
        if(gloo_composer_math_calculated_variables.composer_math_decimal == 'yes' && gloo_composer_math_calculated_variables.composer_math_decimal_seperator != ''){
          gloo_composer_math_calculated_new_value = gloo_composer_math_calculated_new_value.replace(".", gloo_composer_math_calculated_variables.composer_math_decimal_seperator);
        }
    
        $(this).html(gloo_composer_math_calculated_new_value);
      }

      
    });
  }
});