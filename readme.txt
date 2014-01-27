=== Simple Rating ===
Contributors: FlyerUA
Tags: rating, wordpress
Requires at least: 3.0
Tested up to: 3.8.1
Stable tag: 1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Allows users to rate your content.

== Description ==

Simple rating plugin with few features.

Features:

* 4 shapes which come in 5 colors to fit wide variety of themes
* Adjust scale of rating
* Turn on/off display of amount of votes
* Display before or after content
* Display for post, pages or both 
* Disable rating for specific post or page 
* Choose alignment of the rating
* Customize vote count style

== Screenshots ==

1. Simple Rating settings

== Installation ==

1. Upload the simple-rating folder to the /wp-content/plugins/ directory
2. Activate the Simple Rating plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to 'Simple Rating' page under 'Settings' menu

== Frequently Asked Questions ==

**How do I add rating to my posts/pages?**

You can do it in two ways:

* Automatically - set 'Insertion method' option to 'Automatic' (default). Plugin will use content filter to add rating based on your settings.
* Manually - set 'Insertion method' option to 'Manual' and insert `<?php echo spr_show_rating();?>` where you need it in your template.

**Can I reset votes?**

Yes, you can by pressing "Reset votes" button on Simple Rating setting page. 
Note: This only resets votes. Plugin will automatically fully remove its data upon deletion.

**If I don't want to add rating for specific post or page, can I disable it for that specific post or page?**

You certainly can. Just tick the box "Disable rating" when editing post or page in question.

**Can I leave 'Vote count color' empty?**

Yes, you can. In that case, color will be inherited.

**Will you add support for custom post types?**

Yes, that's already planned.

== Changelog ==

= 1.1 =
* Added ability to disable rating for specific post or page 
* Added ability to select way to insert rating: Automatically (via content filter. Default) or Manually (insert wrapper function where you need it)
* Ability to select alignment of the rating
* Ability to change color of vote count text
* Added ability to make vote count bold/italic
* Clean up on deletion of plugin
* Added button to reset votes
* Added link to settings on 'Plugins' page

= 1.0.1 =
Bugfixes:

* Added `IF NOT EXISTS` condition to table creation query
* Fixed embarrassing typo.

= 1.0 =
Initial release