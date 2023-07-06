jQuery(document).ready(
	function()
	{
		var body = jQuery('body');
		var address = window.location.href.replace(/#.*/, '').replace(/.settings-updated=.*/, '');
		if (localStorage[address] != undefined && localStorage[address] == 'LTR')
			body.addClass('ltr-admin');

		jQuery('#wpadminbar .admin-body-ltr-adminbar a.ab-item').click(
			function()
			{
				if (body.hasClass('ltr-admin'))
				{
					localStorage[address] = 'RTL';
					body.removeClass('ltr-admin');
				}
				else
				{
					localStorage[address] = 'LTR';
					body.addClass('ltr-admin');
				}
				return false;
			}
		);
	}
);