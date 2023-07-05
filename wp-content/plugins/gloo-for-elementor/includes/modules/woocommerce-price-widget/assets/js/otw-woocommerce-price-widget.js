jQuery(document).ready(function($) {
  if($('input.variation_id').length >= 1){
    $('body').on('change', 'input.variation_id', function(){
      //function(){
        //Correct bug, I put 0
        if( 0 != $('input.variation_id').val()){
            $('p.price').html($('div.woocommerce-variation-price > span.price').html()).append('<p class="availability">'+$('div.woocommerce-variation-availability').html()+'</p>');
            //console.log($('input.variation_id').val());
            //console.log($('div.woocommerce-variation-price > span.price').html());
        } else {
            $('p.price').html($('div.hidden-variable-price').html());
            if($('p.availability'))
                $('p.availability').remove();
            //console.log('NULL');
        }
      //}
    });
  }
});