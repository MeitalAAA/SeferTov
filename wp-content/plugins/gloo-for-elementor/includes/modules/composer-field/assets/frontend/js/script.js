if (typeof safelyParseJSON != 'function') {
  function safelyParseJSON (json) {
    // This function cannot be optimised, it's best to
    // keep it small!
    var parsed;

    try {
      parsed = JSON.parse(json)
    } catch (e) {
      // Oh well, but whatever...
    }

    return parsed // Could be undefined!
  }
}

if (typeof numberWithCommas != 'function') {
  function numberWithCommas(x, seperator = ',') {
    if(x && x != '0'){
      return x.toLocaleString('en-US').replaceAll(",", seperator);
      // return x.toString().toLocaleString('en-US').replaceAll(",", seperator);
      // return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, seperator);
    }
    else
      return x;
  }
}

if (typeof get_input_name_from_id != 'function') {
  function get_input_name_from_id(input_value){
    if(typeof input_value == 'string' && input_value && input_value.length >= 1){
      return input_value.replace('form-field-','');
    }else{
      return '';
    }
  }
}
if (typeof if_id_exist_in_formula != 'function') {
  function if_id_exist_in_formula(formula){
    let supported_input_types = ['text', 'number', 'tel', 'password', 'email', 'hidden', 'select'];
    for(let i = 0; i < supported_input_types.length; i++){
      input_selector = "input[type='"+supported_input_types[i]+"']";
      if(supported_input_types[i] == 'select')
        input_selector = "select";
      
      jQuery(input_selector).each(function(index, value){
        input_id = jQuery(this).attr('id');
        input_id = get_input_name_from_id(input_id);
        if (typeof input_id == 'string' && input_id && input_id.length >= 1 && formula.indexOf(input_id) !== -1) {
          // input_value = parseInt(jQuery(this).val());
          input_value = jQuery(this).val();
          if(input_value){
            calculated_form_fields[input_id] = input_value;
          }else{
            calculated_form_fields[input_id] = 0;
          }
          
        }
      });
      
    }
  }
}
if (typeof update_input_values != 'function') {
  function update_input_values(){
    for (const key in input_formulas) {
      formula_string = input_formulas[key].composer;
      // console.log(formula_string);
      for (const subLoopKey in calculated_form_fields) {
        if(typeof formula_string == 'string' && formula_string){
          // console.log(formula_string);
          formula_string = formula_string.replace(subLoopKey,calculated_form_fields[subLoopKey]);
        }
        
      }
      // console.log(formula_string);
      input_calculated_value = new Function("return " + formula_string)();
      current_input_value = jQuery('#form-field-'+key).val();
      // console.log(key);
      // console.log(typeof current_input_value);
      // console.log(current_input_value);
      // console.log(typeof input_calculated_value);
      // console.log(input_calculated_value);
      if(input_calculated_value+'' != current_input_value){
        // console.log(input_calculated_value);
        // console.log(key);
        // console.log(jQuery('#form-field-'+key));

        formated_input_calculated_value = gloo_calculation_field_formating(input_calculated_value, input_formulas[key]);

        jQuery('#form-field-'+key).val(input_calculated_value).closest(".elementor-field-group").find('.calculated_field_value').find(".calculation_field_result").html(formated_input_calculated_value);
        jQuery('#form-field-'+key).trigger('change');
        // console.log(formula_string);
        // console.log(input_calculated_value);
      }
      
    }
  }
}

if (typeof gloo_calculation_field_formating != 'function') {
  function gloo_calculation_field_formating(calculated_value, input_options){
    if(calculated_value && typeof input_options == 'object' && input_options){

      if(input_options.decimal == 'yes'){
        if(calculated_value && calculated_value != '0' && input_options.decimal_amount && parseInt(input_options.decimal_amount) >= 0)
          calculated_value = calculated_value.toFixed(input_options.decimal_amount);
      

        if(input_options.decimal_seperator && parseInt(input_options.decimal_seperator) != '')
          calculated_value = calculated_value.replace(".", input_options.decimal_seperator);
      }
      
      
        if(input_options.thousand_seperator_switch == 'yes' && input_options.thousand_seperator_value)
          calculated_value = numberWithCommas(calculated_value, input_options.thousand_seperator_value);
    }
    return calculated_value;
  }
}

if (typeof gloo_calculate_repeater_calculation_again != 'function') {
  function gloo_calculate_repeater_calculation_again(){
    for (const key in gloo_input_repeaters) {
      gloo_repeater_calculation( key);
    }
  }
}

if (typeof gloo_repeater_calculation != 'function') {
  function gloo_repeater_calculation(key){
    // $
        // if(jQuery(this).val()){
          var new_formula_string = '';
          loop_counter = 0;
          // console.log(gloo_input_repeaters[key]);
          jQuery('input[name="form_fields['+gloo_input_repeaters[key].repeater_sub_field_id+'][]"]').each(function(index, item){
            input_value = jQuery(this).val();
            repeater_operation_value = gloo_input_repeaters[key].repeater_operation;
            if(repeater_operation_value && typeof gloo_composer_field_operations[repeater_operation_value] == 'string'){

              if(loop_counter == 0 && gloo_input_repeaters[key].repeater_base_value && gloo_input_repeaters[key].repeater_base_value != '0')
                  new_formula_string += gloo_input_repeaters[key].repeater_base_value;
              if(input_value){
                
                if(loop_counter == 0 && gloo_input_repeaters[key].repeater_base_value && gloo_input_repeaters[key].repeater_base_value != '0'){
                  current_base_value = gloo_input_repeaters[key].repeater_base_value;
                  new_formula_string += gloo_composer_field_operations[repeater_operation_value];
                }
                
                if(gloo_input_repeaters[key].is_percentage && gloo_input_repeaters[key].is_percentage == 'yes'){
                  
                  if(repeater_operation_value == 'multiply' || repeater_operation_value == 'division'){
                    current_item_percentage = new Function("return " + '('+input_value+') / 100')();
                    // console.log(current_item_percentage);
                    if(gloo_input_repeaters[key].is_inverse && gloo_input_repeaters[key].is_inverse == 'yes'){
                      current_item_percentage = new Function("return " + '(1) - (' +current_item_percentage+')')();
                      console.log(current_item_percentage);
                    }
                    current_base_value = new Function("return " + "("+ current_base_value + ")" +gloo_composer_field_operations[repeater_operation_value] + "(" + current_item_percentage + ")")();
                  }else{
                    current_item_percentage = new Function("return " + '('+current_base_value+' * ' +input_value+') / 100')();
                    // console.log(current_item_percentage);
                    if(gloo_input_repeaters[key].is_inverse && gloo_input_repeaters[key].is_inverse == 'yes'){
                      current_item_percentage = new Function("return " + '(100) - (' +current_item_percentage+')')();
                      // console.log(current_item_percentage);
                    }
                    current_base_value = new Function("return " + "("+ current_base_value + ")" +gloo_composer_field_operations[repeater_operation_value] + "(" + current_item_percentage + ")")();
                  }
                  

                  if(loop_counter >= 1 ){

                    // new_formula_string += gloo_composer_field_operations[repeater_operation_value];
                    new_formula_string = current_base_value;

                    // new_formula_string += gloo_composer_field_operations[repeater_operation_value];
                    // new_formula_string += current_item_percentage;

                    
                    // console.log(new_formula_string);
                    // console.log(current_item_percentage);
                    

                  }else{
                    new_formula_string += current_item_percentage;
                  }
                  
                  
                  // current_base_value = 
                }else{
                    
                    if(loop_counter >= 1 )
                      new_formula_string += gloo_composer_field_operations[repeater_operation_value];

                    new_formula_string += input_value;
                    
                  
                }
                loop_counter++;
              }
              
            }
            
          });
          console.log(new_formula_string);
          if(new_formula_string){
            input_calculated_value = new Function("return " + new_formula_string)();
          }else if(typeof gloo_input_repeaters[key].default_value == 'string' && gloo_input_repeaters[key].default_value){
            new_formula_string = gloo_input_repeaters[key].default_value;
            input_calculated_value = new_formula_string;
          }
          else{
            new_formula_string = 0;
            input_calculated_value = new_formula_string;
          }

          formated_input_calculated_value = gloo_calculation_field_formating(input_calculated_value, gloo_input_repeaters[key]);
          
          jQuery('#form-field-'+key).val(new_formula_string).closest(".elementor-field-group").find('.calculated_field_value').find(".calculation_field_result").html(formated_input_calculated_value);
          jQuery('#form-field-'+key).trigger('change');
        // }
        
  }
}
var input_formulas = {};
var calculated_form_fields = {};
var gloo_input_repeaters = {};
var gloo_composer_field_operations = {
  'add': '+',
  'sub':'-',
  'multiply' : '*',
  'division': '/'
};
// calc1 + 2 + calculate_from_repeater

jQuery(document).ready(function($){
  
  jQuery(".gloo_composer_field_input_formula").each(function(){
    
    input_id = jQuery(this).attr('id');
    
    input_id = get_input_name_from_id(input_id);
    input_options = jQuery(this).attr('data-composer-field');
    input_options = safelyParseJSON(input_options);
    if(typeof input_options == 'object' && typeof input_options.composer != 'undefined' && input_options.composer){
      input_formulas[input_id] = input_options;
      if_id_exist_in_formula(input_options.composer);
      if(input_options.is_hidden_field == 'yes'){
        jQuery(this).closest(".elementor-field-group").find('label').hide();
      }
    }
  });

  jQuery(".gloo_composer_field_input_repeater").each(function(){

    

    input_id = jQuery(this).attr('id');
    input_id = get_input_name_from_id(input_id);
    
    input_options = jQuery(this).attr('data-composer-field');
    
    if(input_options){

      input_options = safelyParseJSON(input_options);

      if(typeof input_options == 'object' && input_options && input_options.is_hidden_field == 'yes'){
        jQuery(this).closest(".elementor-field-group").find('label').hide();
      }

      if(typeof input_options == 'object' 
      && typeof input_options.repeater_id != 'undefined' 
      && input_options.repeater_id 
      && typeof input_options.repeater_sub_field_id != 'undefined' 
      && input_options.repeater_sub_field_id
      && typeof input_options.repeater_operation != 'undefined' 
      && input_options.repeater_operation
      ){
        gloo_input_repeaters[input_id] = input_options;
        // if_id_exist_in_formula(input_options.composer);
      }
    }
    
    
  });
  for (const key in gloo_input_repeaters) {
    jQuery("body").on('keyup', 'input[name="form_fields['+gloo_input_repeaters[key].repeater_sub_field_id+'][]"]', function(){
      gloo_repeater_calculation(key);
    });
  }



  
  for (const key in calculated_form_fields) {
    jQuery("body").on('keyup change', '#form-field-'+key, function(){

      input_id = jQuery(this).attr('id');
      input_id = get_input_name_from_id(input_id);

      input_value = jQuery(this).val();
      if(input_value){
        calculated_form_fields[input_id] = input_value;
      }else{
        calculated_form_fields[input_id] = 0;
      }
      update_input_values();
    });
    // console.log(`${key}: ${calculated_form_fields[key]}`);
  }
  update_input_values();

  document.querySelector("body").addEventListener("gloo_repeater_field_item_deleted", (event) => {
    gloo_calculate_repeater_calculation_again();
  });
});