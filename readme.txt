=== Plugin Name ===
Contributors: cais
Donate link: http://buynowshop.com
Tags: posts, tags, featured, multi-widget, user-options
Requires at least: 2.8
Tested up to: 2.8.4
Stable tag: 1.1

Displays most recent posts from a specific featured tag or tags.

== Description ==

Plugin with multi-widget functionality that displays most recent posts from specific tag or tags (set with user options). Also includes user options to display: Author and meta details; comment totals; post categories; post tags; and either full post or excerpt (or any combination).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `bns-featured-tag.php` to the `/wp-content/plugins/` directory
2. Activate through the 'Plugins' menu.
3. Place the BNS Featured Tag widget appropriately in the Appearance | Widgets section of the dashboard.
4. Set options to personal preferences:
* Widget Title
* Tag Names - separated by commas
* Show Author and date/time details of post (checkbox)
* Show all categories attached to post (checkbox)
* Show all tags attached to post (checkbox)
* Show post in full or use default of post excerpt (checkbox)

-- or -

1. Go to 'Plugins' menu under your Dashboard
2. Click on the 'Add New' link
3. Search for bns-featured-tag
4. Install.
5. Activate through the 'Plugins' menu.
6. Place the BNS Featured Tag widget appropriately in the Appearance | Widgets section of the dashboard.
7. Set options to personal preferences:
* Widget Title
* Tag Names - separated by commas
* Show Author and date/time details of post (checkbox)
* Show all categories attached to post (checkbox)
* Show all tags attached to post (checkbox)
* Show post in full or use default of post excerpt (checkbox)

== Frequently Asked Questions ==

= Can I use this in more than one widget area? =

Yes, this plugin has been made for multi-widget compatibility. Each instance of the widget will display, if wanted, differently than every other instance of the widget.

= How can I change the look of the plugin? =

By default, the plugin will use the same style elements used in the "posts" section of the style.css file in the blog's current theme folder.

To have the plugin output look different than the post output simply copy the particular "`.post`" element(s) you want to work with and add the `.widget` class to the beginning of it.

For example, your posts have a black background using:
  `.post {background: #000000;}`
and you want the plugin to have a white background then use:
  `.widget .post {background: #FFFFFF;}`

== Screenshots ==

1. The options panel.

== Changelog ==

= 1.1 =
* added option for Post Titles only
* added <div style="overflow-x: auto"> wrapper to allow for images wider than the widget area

= 1.0 =
* Initial Release.
