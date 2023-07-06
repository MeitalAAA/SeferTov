jQuery(document).ready(function ($) {

  if ($("#gloo_user_agents_extension_send_new_device_email_button").length >= 1) {

    $("#gloo_user_agents_extension_send_new_device_email_button").click(function (e) {
      e.preventDefault();
      $("#gloo_user_agents_extension_send_new_device_email").attr('value', 'yes');
      $(this).parents("form").find("input[type='submit']").trigger('click');
      //$(this).parents("form")[0].submit();
      return false;
    });
  }

  showHideTable(settings_vars.new_logic);

  $('#gloo_user_agents_extension_new_logic').on('change', function () {
    showHideTable(this.value);
  });
  
  function showHideTable(val) {
    if (val == 'yes') {
      $('#old_logic_table').css('display', 'none');
      $('#new_logic_table').css('display', 'block');
    } else {
      $('#new_logic_table').css('display', 'none');
      $('#old_logic_table').css('display', 'block');
    }
  }
});
