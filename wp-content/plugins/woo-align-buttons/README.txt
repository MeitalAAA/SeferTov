=== Woo Align Buttons ===
Contributors: 320up
Donate link: https://320up.com
Tags: woocommerce, align, buttons
Requires at least: 4.7
Tested up to: 6.1.1
Stable tag: 3.6.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin that aligns WooCommerce "Add to cart" buttons.

== Description ==

This lightweight plugin will automatically align your WooCommerce "Add to cart" buttons.

Nothing looks worse than visiting a WooCommerce shop page to find the "Add to cart" buttons scattered all over the place like a dog's breakfast.

This simple plugin solves that problem with a little javascript, and without modifying any core files or templates.

This version will also align your Product Titles thus allowing longer titles or larger fonts.

See FAQ about issue with WooCommerce blocks.

If you have any other issues, let us know in the Support section.
If you like the “Woo Align Buttons” plugin, please take the time to rate it.

== Installation ==

* Upload `woo-align-buttons.zip` through the "Plugins" menu in WordPress
* Else upload unzipped `woo-align-buttons` folder to the `/wp-content/plugins/` directory
* Activate the plugin through the "Plugins" menu in WordPress

== Frequently Asked Questions ==

= How does it work? =

It hooks a wrapper `<div>` around the product information above the "Add to cart" buttons.
Javascript then measures the height of that wrapper and adjusts the height to automatically align the buttons.

= Will it work on all themes? =

No. It is designed to work on **unmodified** WooCommerce files. If your theme has modified any WooCommerce product templates or added hooks that affect those templates or files, this plugin may not work.

It is assumed that if the theme developer has hacked, hooked, or modified any WooCommerce files, they should have already fixed the problem with aligning the buttons.

If it is not working on your theme chances are your theme has been modified or hacked, and this plugin will NOT work.

You can either choose another theme, or contact your theme developer and ask them to fix their theme.

= Can I incorporate it into my own theme? =

Yes. However, the hook in your theme's `functions.php` file will need to be modified slightly.
A sample child theme can be found here on [GitHub](https://github.com/Aquafortis/woo-align-child).

= Will it work with WooCommerce blocks? =

No. WooCommerce has changed the products grid template for their blocks.
Their new template does not allow for separating the 'Add to cart' buttons.
Without the ability to separate the buttons, this plugin can not work.
This issue would need to be taken up with WooCommerce Support.

= Latest update not working? =

There appears to be a handful of users experiencing issues with the latest updates.
This is most likely caused by previous version files being cached on the users system.
You can try clearing all of your cache files.
Otherwise you can revert back to this carbon copy of version 3.6.6:
Download version 100.6.6 from the bottom of the page <a href="https://wordpress.org/plugins/woo-align-buttons/advanced/">Here</a>.
Delete the version you currently have installed, then upload and activate version 100.6.6.
Clear any caches you have, and let us know how you go.
You should not receive any out-of-date warnings with this version.

= Will it work on mobile? =

Yes. Every time the screen is resized, it will re-adjust the buttons.

= Will it work on older versions? =

Don't know. Try it for yourself. It's Free.

= Troubleshooting =

If you are having issues with a modified theme, please leave details in the Support section.
Don't forget to leave a link to your website for us to inspect.
We may be able to give you a link to a custom version that might help. (No guarantees)
**If your theme has been heavily modified and the buttons are outside of the original scope, you may have to consider using another theme.**
If you are able to do some of your own coding, a child theme can usually be made to work.
A sample child theme can be found here on [GitHub](https://github.com/Aquafortis/woo-align-child).

== Screenshots ==

1. Writer Theme.
2. Storefront Theme.
3. Twenty Seventeen Theme.

== Changelog ==

= 3.6.8 =
* Tested on WordPress version 6.1.1
* Tested on WooCommerce version 7.2.2

= 3.6.7 =
* General housekeeping
* FAQ about WooCommerce blocks
* Tested on WordPress version 5.8.1
* Tested on WooCommerce version 5.8.0

= 3.6.6 =
* Tested on WordPress version 5.7.2
* Tested on WooCommerce version 5.4.1

= 3.6.5 =
* Remove WooCommerce version message
* Tested on WordPress version 5.4.1
* Tested on WooCommerce version 4.2.0

= 3.6.4 =
* Changed woo-height to class
* Tested on WordPress version 5.4
* Tested on WooCommerce version 4.0.1

= 3.6.3 =
* Tested on WordPress version 5.3
* Tested on WooCommerce version 3.8.0

= 3.6.2 =
* Tested on WordPress version 5.2.2
* Tested on WooCommerce version 3.7.0
* Includes pure javascript manual option (beta)

= 3.6.1 =
* Tested on WordPress version 5.2
* Tested on WooCommerce version 3.6.2

= 3.6.0 =
* This version also aligns your product titles
* Tested on WooCommerce version 3.6.1

= 3.5.8 =
* Global script can now be called within other Plugins
* Tested on WooCommerce version 3.5.6

= 3.5.7 =
* Custom version for modified themes
* Do not use unless you have issues with your theme
* This version has additional event triggers
* Global folder has been removed
* Tested on WooCommerce version 3.5.6

= 3.5.6 =
* Updated alternate script in global folder
* See Troubleshooting section for usage
* Tested on WooCommerce version 3.5.6

= 3.5.5 =
* Tested on WP version 5.1
* Tested on WooCommerce version 3.5.5

= 3.5.3 =
* Tested on WP version 5.0.2
* Tested on WooCommerce version 3.5.3
* Added global script folder

= 3.4.5 =
* Tested on WP version 4.9.8
* Tested on WooCommerce version 3.4.5
* Added document change function
* Optional scroll function in `woo-align-public.js` file

= 3.4.4 =
* Tested on WP version 4.9.8
* Tested on WooCommerce version 3.4.4

= 3.3.5 =
* Tested on WP version 4.9.5

= 3.1.1 =
* Updated install folder name

= 3.1.0 =
* Initial release

== Upgrade Notice ==

= 3.5.7 =
* No need to upgrade

= 3.3.5 =
* Upgrade to remove WC version warning.

= 3.1.1 =
* No need to upgrade
