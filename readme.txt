=== UK Weather Observations ===
Contributors: Clive Goodhead, Clive Publishing
Donate Link: http://www.clive-publishing.com/free-stuff/oss/uk-weather-observations/
Tags:  BBC, GNU, observations, PHP, plugin, rss, weather, weather observations, widget, WordPress
Requires at least: 2.7
Tested up to: 2.8
Stable tag: 2.0

The UK Weather Observations plugin for WordPress displays recent local weather observations based on RSS feeds published by the BBC.

== Description ==

The UK Weather Observations plugin for WordPress displays recent local weather observations based on RSS feeds published by the BBC. Please see the <a href="http://www.clive-publishing.com/free-stuff/oss/uk-weather-observations/" title="UK Weather Observations">UK Weather Observations web page</a> for more information.

= WordPress UK Weather Observations Features =

* Displays local weather information (worldwide, not just UK), updated regularly during daylight hours; 
* Weather information is sourced from the BBC; 
* Weather information is updated automatically; 
* Admin interface to select locality and customize settings; 
* WordPress Widget enabled; 
* Written specifically for WordPress 2.7+; 
* No PHP skills or file changes are required; 
* And more!

== Installation ==

1. Extract the files from the uk-weather-observations .zip archive.
2. Upload the complete plugin folder /uk-weather-observations/, contained in the .zip file, to your WordPress plugin directory.
3. Go to your WordPress Admin panel -> Plugins and activate the UK Weather Observations plugin.
4. (If required) Go to Appearance -> Widgets and add the UK Weather Observations widget to the sidebar

To put the weather observations information at any place on a post or page, use this shortcode:

[uk-wops]

If you are updating from an earlier version, you can use the WordPress automatic upgrade facility in your blog’s dashboard.

== Configuration ==

The plugin is pre-configured by default to display recent weather observations for Penzance, Cornwall. To change this, 
 
= 1. Go to WP-Admin -> Settings -> UK Weather Observations =

= 2. Enter the BBC location code for the location that you are interested in =

= 3. Click on 'Save Changes' to save the new location and update the weather information for it =

No other configuration is normally required.

To find your BBC location code, go to http://news.bbc.co.uk/weather/ and search for your nearest town or large village.

If the search is successful, your BBC location code is the one to four digit number in the middle of the URL. For example, if you search for ‘Penzance’, the search result URL is 'http://news.bbc.co.uk/weather/forecast/2756?&search=penzance&itemsPerPage=10&region=uk' - the BBC location code is 2756. If the BBC location code is less than four digits add leading zeros, for example, for Oxford use 0025.

Alternatively, download the latest version of 'BBC Weather Observations Codes' from the plugin's web page (http://www.clive-publishing.com/free-stuff/oss/uk-weather-observations/) and search it for your nearest town or large village.

If the search is not successful, try the next nearest town or large village.

== Support ==

If you have comments, problems or suggestions or see something in the code which is not compliant with the WordPress Plugin Codex, please add a comment on my blog at http://www.clive-publishing.com/, create a post at the WordPress support forum (tag it with “uk-weather-observations”) or send an email to clive@clive-publishing.com.

If you think you’ve found a bug, please send an email to clive@clive-publishing.com and I will do my best to fix it.

