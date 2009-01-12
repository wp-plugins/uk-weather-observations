<?php
/*
Plugin Name: UK Weather Observations
Plugin URI: http://www.clive-publishing.com/2009/01/uk-weather-observations-plugin-for-wordpress/
Description: Display up to date UK weather observations.
Version: 1.0
Author: Clive Publishing
Author URI: http://www.clive-publishing.com/
*/
?>
<?php
/* Copyright 2006-2009 Clive Publishing
   (email: clive@clive-publishing.com)

   This program is free software; you can redistribute it and/or 
   modify it under the terms of the GNU General Public License as 
   published by the Free Software Foundation; either version 2 of 
   the License, or (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the 
   Free Software Foundation, Inc., 
   51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
// -------------------------------------------------------------------

// Update weather observations.
function cp_wobs_update() {
$new_data = cp_wobs_getdata();
if ($new_data) update_option('cp_wobs_output', $new_data);
else {
	$no_obs = get_option('cp_wobs_noobs');
	if ($no_obs == '')
		$no_obs = 'No current observations available';
	update_option('cp_wobs_output', $no_obs);
	}
update_option('cp_wobs_lastrun', date('r'));
}

// Set up update schedule on plugin activation.
register_activation_hook( __FILE__, 'cp_wobs_activate');
// The website needs to have regular visitors to keep the weather 
// information up to date, because a new update may not finish before 
// a page/post/widget is displayed.
add_action('cp_wobs_hourly', 'cp_wobs_update', 1);

function cp_wobs_activate() {
wp_schedule_event(time(), 'hourly', 'cp_wobs_hourly');
wp_schedule_event((time()+1800), 'hourly', 'cp_wobs_hourly');
cp_wobs_update();
}

// Cancel update schedule on plugin deactivation
// and remove data from database.
function cp_wobs_deactivate() {
wp_clear_scheduled_hook('cp_wobs_hourly');
delete_option('cp_wobs_output');
delete_option('cp_wobs_noobs');
delete_option('cp_wobs_bbc');
delete_option('cp_wobs_location');
delete_option('cp_wobs_lastrun');
}
register_deactivation_hook( __FILE__, 'cp_wobs_deactivate');

// Display weather observations.
function cp_wobs_display() {
return get_option('cp_wobs_output');
}

// Shortcode support.
if (function_exists('add_shortcode'))
	add_shortcode('uk-wobs', 'cp_wobs_display');

function cp_wobs_getdata() {
$bbc = get_option('cp_wobs_bbc');
if ($bbc == '')
	$bbc = 'http://feeds.bbc.co.uk/weather/feeds/rss/obs/id/';
$loc = get_option('cp_wobs_location');
if (!preg_match('!\d\d\d\d!', $loc))
	$loc = '3737';      // Default location: Marazion, Cornwall.
$f = $bbc . $loc . '.xml';

if (!($data = @file_get_contents($f))) return 0;
if (!preg_match('!<channel>.*</channel>!s', $data, $matches))
	return 0;
$data = $matches[0];
if (!preg_match('!<title>(.*)</title>.*<item>!s', $data, $matches))
	return 0;
if (preg_match('!BBC - Weather Centre - Latest Observations for (.*)!s', 
     $matches[1], $matches))
	$location = trim($matches[1]);
if (preg_match('!(.*), United Kingdom!s', $matches[1], $matches))
	$location = trim($matches[1]);
if (!preg_match('!<item>.*</item>!s', $data, $matches)) return 0;
$data = $matches[0];
if (!preg_match('!<title>(.*)</title>!s', $data, $matches)) return 0;
if (preg_match('!(.*):.*!s', $matches[1], $matches))
	$time = trim($matches[1]);
if (preg_match('!.*day at (.*)!s', $time, $matches))
	$time = trim($matches[1]);
if (!preg_match('!<description>(.*)</description>!s', $data, $matches))
	return 0;
$description = trim($matches[1]);
// Required to deal with Pressure changes, 
// otherwise could split on commas.
$description = preg_replace('!, !', '|', $description);
$description = preg_replace('!\|([^:]+?\|)!', ' $1', $description);
$items = split('\|', $description);

$output = "\n<!-- UK Weather Observations Plugin -->\n";
$output .= "<!-- by Clive Publishing (http://www.clive-publishing.com/) -->\n";
$output .= '<div class="cp_wobs">' . "\n";
$output .= "<p>Weather observation for $location at $time.</p>\n";
$output .= "<table>\n";
foreach($items as $vals) {
  $val = split(':', $vals);
  $output .= "<tr>\n";
  $output .= '<td class="cp_wobs_th" valign="top">' . trim($val[0]);
  $output .= ": </td>\n";
  $output .= '<td valign="top">' . trim($val[1]) . "</td>\n";
  $output .= "</tr>\n";
  }
$output .= "</table>\n";
$output .= '<p>Weather information derived from data from bbc.co.uk';
$output .= '<a href="http://www.clive-publishing.com/2009/01/';
$output .= 'uk-weather-observations-plugin-for-wordpress/">.</a></p>';
$output .= "\n</div>\n";

return $output;
}

// -------------------------------------------------------------------
// Admin menu control.
function cp_wobs_admin_menu() {
add_options_page('UK Weather Observations', 'UK Weather Observations', 
                 'activate_plugins', __FILE__, 'cp_wobs_plugin_options');
}
add_action('admin_menu', 'cp_wobs_admin_menu');

function cp_wobs_plugin_options() {
$loc = get_option('cp_wobs_location');
$bbc = get_option('cp_wobs_bbc');
$noobs = get_option('cp_wobs_noobs');
$hidden_field_name = 'cp_wobs_hidden';

// If the user has posted new options read and update them.
if ($_POST[$hidden_field_name] == 'Y' ) {
	$loc = $_POST['cp_wobs_location'];
	update_option('cp_wobs_location', $loc);
	$noobs = $_POST['cp_wobs_noobs'];
	update_option('cp_wobs_noobs', $noobs);
	$bbc = $_POST['cp_wobs_bbc'];
	update_option('cp_wobs_bbc', $bbc);
	cp_wobs_update();  // Update with new options.
	// Put an options updated message on the screen
	echo '<div class="updated"><p><strong>Options saved.</strong></p></div>';
	}
$lastupdate = get_option('cp_wobs_lastrun');
echo <<<END
<div class="wrap">
<form method="post" action="">
<h2>UK Weather Observations settings</h2>
<p>Last UK Weather Observations database update: $lastupdate.</p>
<h3>Basic Options</h3>
<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
<tr>
<th width="30%" valign="top" style="padding-top: 10px;">
<label for="cp_wobs_location">Location:</label>
</th>
<td>
<input type="text" size="6" name="cp_wobs_location" 
 id="cp_wobs_location" value="$loc" />
<p style="margin: 5px 10px;" class="setting-description">Enter the 
4 digit BBC code for your selected location. If no code is entered, 
weather observations for Marazion, Cornwall (code 3737) will be 
displayed.</p>
</td>
</tr>
<tr>
<th width="30%" valign="top" style="padding-top: 10px;">
<label for="cp_wobs_noobs">No observations message:</label>
</th>
<td>
<input type="text" size="20" name="cp_wobs_noobs" 
 id="cp_wobs_noobs" value="$noobs" />
<p style="margin: 5px 10px;" class="setting-description">Enter 
the message that you require displayed when no current weather 
observations are available for the chosen location. If no 
message is entered, 'No current observations available' will be 
displayed.</p>
</td>
</tr>
</table>
<h3>Advanced Options</h3>
<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
<tr>
<th width="30%" valign="top" style="padding-top: 10px;">
<label for="cp_wobs_bbc">BBC file directory:</label>
</th>
<td>
<input type="text" size="50" name="cp_wobs_bbc" 
 id="cp_wobs_bbc" value="$bbc" />
<p style="margin: 5px 10px;" class="setting-description">This is 
required only if the BBC change the location of their weather 
feeds between updates of the plugin. If no directory is entered, 
the default of 'http://feeds.bbc.co.uk/weather/feeds/rss/obs/id/' 
will be used. Include the trailing '/'.</p>
</td>
</tr>
</table>
<p class="submit">
<input type="hidden" name="$hidden_field_name" value="Y">
<input type='submit' name='info_update' value='Save Changes' />
</p>
</form>
</div>
END;
}

// -------------------------------------------------------------------
// WordPress Widget support.

function cp_wobs_widget_init() {
if(!function_exists('register_sidebar_widget')) return;
function cp_wobs_widget($args) {

extract($args);
echo $before_widget;
echo $before_title;
echo "Weather";
echo $after_title;
echo cp_wobs_display();
echo $after_widget;

}
register_sidebar_widget('UK Weather Observations', 'cp_wobs_widget');

}
add_action('plugins_loaded', 'cp_wobs_widget_init');

?>