=== Simple Rating ===
Contributors: FlyerUA
Tags: rating, wordpress
Requires at least: 3.0
Tested up to: 3.8.1
Stable tag: 1.3.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Allows users to rate your content.

== Description ==

Simple rating plugin with few features.

= Features: =
* 4 shapes which come in 5 colors to fit wide variety of themes
* Adjust scale of rating
* Turn on/off display of amount of votes
* Display before or after content
* Select for what types of content rating will be displayed
* Disable rating for specific entry
* Choose alignment of the rating
* Customize vote count style
* Widget that lists your top rated content 
* Works with custom posts types
* Supports aggregated rating to show rating in search engine's snippets
* Allow or disallow guests to vote
* Rating statistics metabox

== Screenshots ==

1. Simple Rating settings
2. Disable rating for specific post
3. Top Rated Content widget
4. Example of Google Search snippet when Aggregated rating is enabled
5. Rating statistics metabox which shows what votes were cast for current entry

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

You certainly can. Just tick the box "Disable rating" when editing/publishing post or page in question.

**Can I leave 'Vote count color' empty?**

Yes, you can. In that case, color will be inherited.

**Are custom post types supported?**

Yes, they are. 

== Changelog ==

= 1.3.1 =
* Fixed bug with alignment. It now works properly. Thanks to alkahf for reporting
* Fixed bug with styling when displaying rating in loops

= 1.3 =
* Added Aggregated Rating (see 4th screenshot for example) functionality. Your rating can now be shown in search engines' snippets
* Added notification when settings are successfully updated 
* Added metabox which shows rating statistics
* You can now allow guests to vote 
* Added tooltips to make configuration easier and more understandable. Thanks to Chris Bracco for tooltips style
* Fixed bug in live preview
* Fixed bug in widget

= 1.2.1 =
* Added ability to show rating in the loop on the home page
* Fixed bug with displaying in archives which could be experienced when using manual insertion

= 1.2 =
* Added widget that lists your top rated content
* As promised, Simple Rating now works with custom post types (Please, go to settings and re-select where to show rating)
* Added mechanism to ensure that rating will be added only once when using manual insertion method
* Rating won't show on archive pages (category, tag, custom post type archive, etc) if rating is disabled for that post type
* Minified CSS and JS to save 1,34 Kb. Imagine all saved bandwidth ;)

= 1.1.2 =
* Fixed bug with "Show in loops" functionality which could be experienced when using manual insertion method

= 1.1.1 =
* Added ability to show rating in loops (category pages for example) (requested by user alkahf)

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