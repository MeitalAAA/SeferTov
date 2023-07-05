
jQuery(document).ready(function($){

  $("body").on('click', '.add_new_li_button', function(e){
    parent_container = $(this).parent(".add_new_li_button_parent");
    html_container = $(this).attr("data-target");
    parent_container.find("ul.repeatable_fields").append('<li>'+$("."+html_container).html()+'</li>');
    
    if(parent_container.find("ul.repeatable_fields li").length <= 0){
      parent_container.find("ul.repeatable_fields li .removebutton_container").hide();
    }else{
      parent_container.find("ul.repeatable_fields li .removebutton_container").show();
    }
    e.preventDefault();
    return false;
  });


  $("body").on('click', 'a.removeButton', function(e){
    e.preventDefault();
    if($(this).parents("ul").children("li").length <= 0){
      $(this).parents("ul").find(".removebutton_container").hide();
    }else{
      $(this).parents("ul").find(".removebutton_container").show();
    }
    $(this).parents("li").remove();
    return false;
  });

});
