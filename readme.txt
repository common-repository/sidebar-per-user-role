=== Sidebar Per User Role ===
Contributors: bainternet 
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PPCPQV
Tags: user, role, sidebar per user, per role, sidebars
Requires at least: 3.0.0
Tested up to: 4.7.0
Stable tag: 0.3

This Plugin lets you display a sidebar per user role

== Description ==

This Plugin lets you display a sidebar per user role automaticaly without any coding what so ever, and it works with any theme out of the box.

Any feedback or suggestions are welcome.

Also check out my [other plugins](http://en.bainternet.info/category/plugins)
 

== Installation ==

*   Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation.
*   Then activate the Plugin from Plugins page.
*   Done!

== Screenshots ==
1. User Role Sidebars
2. Plugin option panel
3. Plugin help panel

== Frequently Asked Questions ==

Usage: 

Either call the guest sidebar in your theme using :

 

`<?php dynamic_sidebar( 'guest-sidebar' ); ?>`

 

which will be replaced based on the user role.

 

Or use an existing sidebar you want replaced by adding this in your themes functions.php

 

`add_action('after_theme_setup','replace_sidebar_');

function replace_sidebar_(){

    global $sidebars_per_role;
    $sidebars_per_role->sidebar_to_replace = 'ID-OF-YOUR-Sidebar';
}`


== Changelog ==
0.3 Fixed naming issues
0.2 Fixed require_once error.
0.1 Initial relases.