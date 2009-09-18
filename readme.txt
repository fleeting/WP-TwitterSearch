=== WP-TwitterSearch ===
Contributors: James Fleeting
Donate link: http://paperkilledrock.com/projects
Tags: twitter, search, widget
Requires at least: 2.7
Tested up to: 2.8.4
Stable tag: 1.6

Displays the latest results based on a twitter search. Options include setting multiple search terms and limiting tweets shown.

== Description ==

Displays the latest results based on a twitter search. Options include setting multiple search terms, excluding terms and limiting tweets shown. Use the sidebar widget, <code>&lt;?php wp_twittersearch_feed(); ?&gt;</code> in your template or shortcode in your posts/pages: [wpts terms=twittersearch limit=5 lang=en]

* This plugin requires PHP5. Contact your host if your unsure what version you have or if your stuck on PHP4.

== Screenshots ==

1. WP-TwitterSearch Options Page
2. WP-TwitterSearch Widget Options

== Installation ==

Installing WP-TwitterSearch is as easy as any other plugin.

1. Upload `WP-TwitterSearch` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the WP-TwitterSearch Widget, place `<?php wp_twittersearch_feed(); ?>` in your template, or use the shortcode in posts/pages: [wpts terms=twittersearch limit=5 lang=en]
1. See the TwitterSearch menu for options.

== Frequently Asked Questions ==

= I get this error: Fatal error: Call to undefined function: simplexml_load_file() =

WP-TwitterSearch requires PHP5 and that error means your using PHP4. I have no plans to make it work with PHP4. You can contact your host for more info about your server supporting PHP5.

= Where is the widget? =

As of v1.1 you can now use the sidebar widget to display the twittersearch feed. This is currently limited to one widget and can have its own search terms and limit. The next release will support multiple widgets with independent options.

= How do I display my latest tweet? =

There are a ton of great plugins that display your tweets, there was no need to create another. This plugin is for search.twitter.com only.

= Do I have to provide credit when using the plugin? =

Nope, you are free to use it however you like no credit needed. However, if you wish to provide a link back to this plugin there is an option to enable credit. This defaults to no, but if switched to yes a 'powered by' will show up below the plugin on your site. Again, this is default to no.

= Does it work on WordPress version x.x? =

Honestly, I have no clue. I have tested it on version 2.7+ and 2.8. I don't have installs of anything before 2.7 so it could work before that. I also don't plan to test or make it work on older versions if it doesn't. For security alone you should always be using the latest version.

= I have another question! =

Good, you can either email me at james.fleeting[at]gmail.com or post in the wordpress forums. I'm always browsing around.

== Future Plans ==

1. Add the remaining Twitter Advanced Search options
1. Allow for multiple widgets each with its own options
1. Location based search (input a zip code and distance)
1. Add a button to the Visual Editor to add a shortcode
1. Display results from a username and tags from a post
1. Cache the result for a period of time?
1. Should the tweets display in an unordered list? divs/spans? Should you be able to choose?

== Change Log ==

= v1.6 =
* Released: 9/18/2009
* Updated to use WP 2.7+ register_settings
* Plugin menu is now located under 'Settings'
* Removed Top Level Menu and About page
* The plugin is now a class so it will play well with others

= v1.5.5 =
* Released: 9/8/2009
* Fixed problem when excluding multiple words
* Fixed empty xml error if twitter returns nothing
* Display message instead of xml errors

= v1.5.4 =
* Released: 9/5/2009
* Added Exclude Search Terms

= v1.5.3 =
* Released: 7/10/2009
* Fixed Generic Variable Name

= v1.5.2 =
* Released: 7/7/2009
* Added From This User Option
* Added Search Phrase Option
* Fixed Widget Title
* Updated Screenshots

= v1.5.1 =
* Released: 7/5/2009
* Fixed all the stuff I messed up over svn

= v1.5 =
* Released: 7/5/2009
* Added Shortcode Option
* Added Widget Options
* Added Avatar Support
* Added Format Date
* Added Language Option
* Added Display Name Option
* Added Permalink to Date
* Updated html output

= v1.2.1 =
* Released: 5/27/2009
* Fixed date format
* Fixed quotes in tweets
* Fixed failure when using #hashtag

= v1.2 =
* Released: 5/26/2009
* Added tweet date
* Added tweet source

= v1.1 =
* Released: 5/2/2009
* Added widget support
* Updated plugin path
* Added screenshots and icon

= v1.0 =
* Released: 4/30/2009
* Initial release of the WP-TwitterSearch plugin