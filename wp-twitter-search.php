<?php 
/*
Plugin Name: WP-TwitterSearch
Version: 1.6
Plugin URI: http://paperkilledrock.com/projects/WP-TwitterSearch
Description: Displays the latest results based on a twitter search. Options include setting multiple search terms and limiting tweets shown. Add the widget to your sidebar, use <code>&lt;?php wp_twittersearch_feed(); ?&gt;</code> in your template or the shortcode in your posts or pages: [wpts terms=twittersearch limit=5 lang=en].
Author: James Fleeting
Author URI: http://jamesfleeting.com/
*/

/*  Copyright 2009  James Fleeting (james.fleeting[at]gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//lets go ahead and define a few things for easy updating...
define(WPTS_CURRENT_VERSION, "1.6");
define(WPTS_PLUGIN_URL, "http://paperkilledrock.com/projects/WP-TwitterSearch");
global $wp_version;

class WPTwitterSearch {
  
  function WPTwitterSearch() {
    //guess we should add some wordpress hooks and actions
    add_action('admin_init', array(&$this, 'register_wptwittersearch_settings'));
    register_deactivation_hook(__FILE__, array(&$this, 'unregister_wptwittersearch_settings'));
    add_action('admin_menu', array(&$this, 'wp_twitter_search_menu'));
    add_action('wp_dashboard_setup', array(&$this, 'add_wptwittersearch_dashboard_widgets'));
    add_action('plugins_loaded', array(&$this, 'wp_twitter_search_widget_init'));
    add_shortcode('wpts', array(&$this, 'wp_twittersearch_shortcode'));  
  }

  function register_wptwittersearch_settings() { // whitelist options
    register_setting( 'wpts-group', 'wptwittersearch_limit' );
    register_setting( 'wpts-group', 'wptwittersearch_terms' );
    register_setting( 'wpts-group', 'wptwittersearch_phrase' );
    register_setting( 'wpts-group', 'wptwittersearch_nots' );
    register_setting( 'wpts-group', 'wptwittersearch_author' );
    register_setting( 'wpts-group', 'wptwittersearch_avatar' );
    register_setting( 'wpts-group', 'wptwittersearch_date' );
    register_setting( 'wpts-group', 'wptwittersearch_dateformat' );
    register_setting( 'wpts-group', 'wptwittersearch_lang' );
    register_setting( 'wpts-group', 'wptwittersearch_name' );
    register_setting( 'wpts-group', 'wptwittersearch_linklove' );
  }
  
  function unregister_wptwittersearch_settings() { // whitelist options
    unregister_setting( 'wpts-group', 'wptwittersearch_limit' );
    unregister_setting( 'wpts-group', 'wptwittersearch_terms' );
    unregister_setting( 'wpts-group', 'wptwittersearch_phrase' );
    unregister_setting( 'wpts-group', 'wptwittersearch_nots' );
    unregister_setting( 'wpts-group', 'wptwittersearch_author' );
    unregister_setting( 'wpts-group', 'wptwittersearch_avatar' );
    unregister_setting( 'wpts-group', 'wptwittersearch_date' );
    unregister_setting( 'wpts-group', 'wptwittersearch_dateformat' );
    unregister_setting( 'wpts-group', 'wptwittersearch_lang' );
    unregister_setting( 'wpts-group', 'wptwittersearch_name' );
    unregister_setting( 'wpts-group', 'wptwittersearch_linklove' );
  }
  
  //create menu items
  function wp_twitter_search_menu() {  
    add_options_page('WP-TwitterSearch Settings', 'TwitterSearch', 8, 'wptwittersearch', array(&$this, 'wp_twitter_search_settings'));
  }
  
  //create dashboard widgets
  function add_wptwittersearch_dashboard_widgets() {
    wp_add_dashboard_widget('wptwittersearch', 'WP-TwitterSearch', array(&$this, 'wp_twitter_search_dashboard'), $control_callback = null);
  } //add_wptwittersearch_dashboard_widgets
  
  //search dashboard widget
  function wp_twitter_search_dashboard() {
    echo $this->wp_twittersearch_feed();
    echo '<span class="wpts_linklove">Powered by <a href="' . WPTS_PLUGIN_URL . '">WP-TwitterSearch</a></span>';
  } //wp_twitter_search_dashboard
  
  //register sidebar widget
  function wp_twitter_search_widget_init() {
    wp_register_sidebar_widget(wptwittersearch_widget, 'TwitterSearch', array(&$this, 'wp_twitter_search_widget'), array('description' => __('Add TwitterSearch To Your Sidebar.')) );
    wp_register_widget_control(wptwittersearch_widget, __('WP TwitterSearch'), array(&$this, 'widget_twitter_search_control'));
  } // wp_twitter_search_widget_init
  
  //the sidebar widget - SINGLE
  function wp_twitter_search_widget($args) {
    extract($args, EXTR_SKIP);
    $options = get_option(wptwittersearch_widget);  
    $wpts_widget_title = apply_filters('widget_title', $options["wpts_widget_title"]);
    $wpts_widget_terms = $options['wpts_widget_terms'];
    $wpts_widget_limit = $options['wpts_widget_limit'];
    $wpts_widget_limit = $options['wpts_widget_nots'];
    
    echo $before_widget;
    if ($wpts_widget_title) {
     echo $args['before_title'] . $wpts_widget_title . $args['after_title']; 
    }
    wp_twittersearch_feed($wpts_widget_terms, $wpts_widget_limit, $wpts_widget_nots);
    echo $after_widget;
  } //wp_twitter_search_widget
  
  //manage widget settings - SINGLE
  function widget_twitter_search_control() {
    $options = get_option(wptwittersearch_widget);
    if (!is_array($options)) {
      $options = array();
    }

    $widget_data = $_POST[wptwittersearch_widget];
    if ($widget_data['submit']) {
      $options['wpts_widget_title'] = $widget_data['wpts_widget_title'];
      $options['wpts_widget_terms'] = $widget_data['wpts_widget_terms'];
      $options['wpts_widget_limit'] = $widget_data['wpts_widget_limit'];
      $options['wpts_widget_nots']  = $widget_data['wpts_widget_nots'];

      update_option(wptwittersearch_widget, $options);
    }

    // Render form
    $wpts_widget_title = $options['wpts_widget_title'];
    $wpts_widget_terms = $options['wpts_widget_terms'];
    $wpts_widget_limit = $options['wpts_widget_limit'];
    $wpts_widget_nots  = $options['wpts_widget_nots'];

    ?>
    <p>
      <label for="wptwittersearch_widget-wpts_widget_title">Title:</label>
      <input class="widefat" type="text" name="wptwittersearch_widget[wpts_widget_title]" id="wptwittersearch_widget-wpts_widget_title" value="<?php echo $wpts_widget_title; ?>"/>
    </p>
    
    <p>
      <label for="wptwittersearch_widget-wpts_widget_terms">Search Terms:</label>
      <input class="widefat" type="text" name="wptwittersearch_widget[wpts_widget_terms]" id="wptwittersearch_widget-wpts_widget_terms" value="<?php echo $wpts_widget_terms; ?>"/>
    </p>
    
    <p>
      <label for="wptwittersearch_widget-wpts_widget_nots">Exclude These Terms:</label>
      <input class="widefat" type="text" name="wptwittersearch_widget[wpts_widget_nots]" id="wptwittersearch_widget-wpts_widget_terms" value="<?php echo $wpts_widget_nots; ?>" />
    </p>
    
    <p>
      <label for="wptwittersearch_widget-wpts_widget_limit">Number to show:</label>
      <input class="widefat" type="text" name="wptwittersearch_widget[wpts_widget_limit]" id="wptwittersearch_widget-wpts_widget_limit" value="<?php echo $wpts_widget_limit; ?>"/>
    </p>
    
    <p>For more general options check out the WP-TwitterSearch Settings page.</p>
    
    <input type="hidden" name="wptwittersearch_widget[submit]" value="1"/>
    <?php
  } //widget_twitter_search_control
  
  //user defined options (values are stored in database in wp_options)
  function wp_twitter_search_settings() {
?>
    <style>
      .search {
      	margin:10px;
      }
      .tweet h3, .tweet h3 a {
      	color:#666;
      	margin:0;
      	text-decoration:none;
      	border:0;
      }
      .tweet h3 a:hover {
        border-bottom:1px solid #666;
      }
      .tweet {
      	background: #FFFFFF;
      	color: #666666;
      	text-align:left;
      	padding:10px;
      	margin:0 0 2px;
      	width:auto;
      	-moz-border-radius:10px;
      	-webkit-border-radius:10px;
      	overflow:hidden;
      	position:relative;
      }
      .tweet p span.tweet_date {
        font-size: 10px;
      }
      /* The b tag is used to highlight the search keyword on the resulting search page */
      b {
      	background: #a9c9d9;
      	padding:1px 3px;
      }
      .avatar_border {
      	border:1px solid #EFEFEF;
      	float:left;
      	margin:0 10px 0 0;
      	overflow:hidden;
      	padding:0;
      }
      .avatar {
      	background:#CCCCCC;
      	border:6px solid #F0F0F0;
      	margin:2px;
      	padding:2px;
      }
      /* It's important to manually set the height and width as Twitter doesn't always reduce the avatar images and some may display a much larger image */
      img.avatar {
      	height:48px;
      	width:48px;
      }
      
    </style>
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script>
    
    <div class="wrap">
      <h2>WP-TwitterSearch Settings</h2>
        
        <form method="post" action="options.php">
        <?php wp_nonce_field('update-options'); ?>
        <?php settings_fields('wpts-group'); ?>
          <table class="form-table">
            <tr valign="top">
              <th scope="row">Search Terms</th>
              <td><input type="text" name="wptwittersearch_terms" value="<?php echo get_option('wptwittersearch_terms'); ?>" /></td>
              <td><span></span></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">Search Phrase</th>
              <td><input type="text" name="wptwittersearch_phrase" value="<?php echo get_option('wptwittersearch_phrase'); ?>" /></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">Exclude These Terms</th>
              <td><input type="text" name="wptwittersearch_nots" value="<?php echo get_option('wptwittersearch_nots'); ?>" /></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">From This Person</th>
              <td><input type="text" name="wptwittersearch_author" value="<?php echo get_option('wptwittersearch_author'); ?>"></td>
            </tr>
             
            <tr valign="top">
              <th scope="row">Limit Tweets</th>
              <td><input type="text" name="wptwittersearch_limit" value="<?php echo get_option('wptwittersearch_limit'); ?>" /></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">Display User Avatar?</th>
              <td><select name="wptwittersearch_avatar">
                <option value="1"<?php if (get_option('wptwittersearch_avatar') == '1') { echo ' selected'; } ?>>Yes</option>
								<option value="0"<?php if (get_option('wptwittersearch_avatar') == '0') { echo ' selected'; } ?>>No</option>
							</select></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">How to Display Name?</th>
              <td><select name="wptwittersearch_name">
                <option value="1"<?php if (get_option('wptwittersearch_name') == '1') { echo ' selected'; } ?>>Display Name</option>
								<option value="0"<?php if (get_option('wptwittersearch_name') == '0') { echo ' selected'; } ?>>Username</option>
							</select></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">Display Tweet Date?</th>
              <td><select name="wptwittersearch_date">
                <option value="1"<?php if (get_option('wptwittersearch_date') == '1') { echo ' selected'; } ?>>Yes</option>
								<option value="0"<?php if (get_option('wptwittersearch_date') == '0') { echo ' selected'; } ?>>No</option>
							</select></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">Date Format:</th>
              <td><input type="text" name="wptwittersearch_dateformat" value="<?php echo get_option('wptwittersearch_dateformat'); ?>" /><br />
              <small>use <a href="http://us.php.net/manual/en/function.date.php">php date()</a> params to change the format</small></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">Written in:</th>
              <td><select name="wptwittersearch_lang">
                <option value="all"<?php if (get_option('wptwittersearch_lang') == 'all') { echo ' selected'; } ?>>Any Language</option>
                <option value="ar"<?php if (get_option('wptwittersearch_lang') == 'ar') { echo ' selected'; } ?>>Arabic (العربية)</option>
                <option value="da"<?php if (get_option('wptwittersearch_lang') == 'da') { echo ' selected'; } ?>>Danish (dansk)</option>
                <option value="nl"<?php if (get_option('wptwittersearch_lang') == 'nl') { echo ' selected'; } ?>>Dutch (Nederlands)</option>
                <option value="en"<?php if (get_option('wptwittersearch_lang') == 'en') { echo ' selected'; } ?>>English</option>
                <option value="fi"<?php if (get_option('wptwittersearch_lang') == 'fi') { echo ' selected'; } ?>>Finnish (suomen kieli)</option>
                <option value="fr"<?php if (get_option('wptwittersearch_lang') == 'fr') { echo ' selected'; } ?>>French (français)</option>
                <option value="de"<?php if (get_option('wptwittersearch_lang') == 'de') { echo ' selected'; } ?>>German (Deutsch)</option>
                <option value="hu"<?php if (get_option('wptwittersearch_lang') == 'hu') { echo ' selected'; } ?>>Hungarian (Magyar)</option>
                <option value="is"<?php if (get_option('wptwittersearch_lang') == 'is') { echo ' selected'; } ?>>Icelandic (Íslenska)</option>
                <option value="it"<?php if (get_option('wptwittersearch_lang') == 'it') { echo ' selected'; } ?>>Italian (Italiano)</option>
                <option value="ja"<?php if (get_option('wptwittersearch_lang') == 'ja') { echo ' selected'; } ?>>Japanese (日本語)</option>
                <option value="no"<?php if (get_option('wptwittersearch_lang') == 'no') { echo ' selected'; } ?>>Norwegian (Norsk)</option>
                <option value="pl"<?php if (get_option('wptwittersearch_lang') == 'pl') { echo ' selected'; } ?>>Polish (polski)</option>
                <option value="pt"<?php if (get_option('wptwittersearch_lang') == 'pt') { echo ' selected'; } ?>>Portuguese (Português)</option>
                <option value="ru"<?php if (get_option('wptwittersearch_lang') == 'ru') { echo ' selected'; } ?>>Russian (русский язык)</option>
                <option value="es"<?php if (get_option('wptwittersearch_lang') == 'es') { echo ' selected'; } ?>>Spanish (español)</option>
                <option value="sv"<?php if (get_option('wptwittersearch_lang') == 'sv') { echo ' selected'; } ?>>Swedish (Svenska)</option>
                <option value="th"<?php if (get_option('wptwittersearch_lang') == 'th') { echo ' selected'; } ?>>Thai (ไทย)</option>
              </select></td>
            </tr>            
            
            <tr valign="top">
              <th scope="row">Credit WP-TwitterSearch</th>
              <td><select name="wptwittersearch_linklove">
                <option value="1"<?php if (get_option('wptwittersearch_linklove') == '1') { echo ' selected'; } ?>>Yes</option>
								<option value="0"<?php if (get_option('wptwittersearch_linklove') == '0') { echo ' selected'; } ?>>No</option>
							</select></td>
            </tr>
          </table>

          <input type="hidden" name="action" value="update" />

          <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
          </p>
        </form>
        <h2>Search Preview</h2>
          <span><?php echo $this->wp_twittersearch_feed(); ?></span>
          <p><a href="?page=<?php echo plugin_basename(__FILE__); ?>" class="button">Refresh Feed</a></p>
    </div>

<?php   
  } //wp_twitter_search_settings
  
  //lets handle the shortcode
  function  wp_twittersearch_shortcode($atts = null) {
    extract(shortcode_atts(array(
      "terms" => get_option('wptwittersearch_terms'),
      "limit" => get_option('wptwittersearch_limit'),
      "lang"  => get_option('wptwittersearch_lang'),
      //"date"  => true,
      ), $atts));
      wp_twittersearch_feed($terms, $limit, $lang);
  } //wp_twittersearch_shortcode
  
  // The heart of the plugin.
  function wp_twittersearch_feed($terms = null, $limit = null, $lang = null, $exclude = null) {
    
    //figure out the search terms
    if (isset($_GET['search_terms']) && $_GET['search_terms'] != '') {
      $search_terms = $_GET['search_terms'];
    } elseif (isset($terms) && $terms != '') {
      $search_terms = $terms;
    } else {
      $search_terms = get_option('wptwittersearch_terms');
    }
    
    //figure out the search phrase
    if (isset($_GET['search_phrase']) && $_GET['search_phrase'] != '') {
      $search_phrase = $_GET['search_phrase'];
    } elseif (isset($phrase) && $phrase != '') {
      $search_phrase = $phrase;
    } else {
      $search_phrase = get_option('wptwittersearch_phrase');
    }
    
    //figure out the terms to exclude
    if (isset($_GET['exclude_terms']) && $_GET['exclude_terms'] != '') {
      $exclude_terms = $_GET['exclude_terms'];
    } elseif (isset($exclude) && $exclude != '') {
      $exclude_terms = $exclude;
    } else {
      $exclude_terms = get_option('wptwittersearch_nots');
    }
    
    //figure out if we limit tweets to an author
    if (isset($_GET['this_person']) && $_GET['this_person'] != '') {
      $search_person = $_GET['this_person'];
    } elseif (isset($this_person) && $this_person != '') {
      $search_person = $this_person;
    } else {
      $search_person = get_option('wptwittersearch_author');
    }
    
    //figure out number of tweets to return
    if (isset($_GET['limit_tweets']) && $_GET['limit_tweets'] != '') {
      $limit_tweets = $_GET['limit_tweets'];
    } elseif (isset($limit) && $limit != '') {
      $limit_tweets = $limit;
    } else {
      $limit_tweets = get_option('wptwittersearch_limit');
    }
    
    //figure out what language to search
    if (isset($_GET['search_lang']) && $_GET['search_lang'] != '') {
      $search_lang = $_GET['search_lang'];
    } elseif (isset($lang) && $lang != '') {
      $search_lang = $lang;
    } else {
      $search_lang = get_option('wptwittersearch_lang');
    }
    
    $search_terms  = str_replace('#', '%23', $search_terms);
    $search_terms  = str_replace('"', '%22', $search_terms);
    $search_terms  = str_replace(' ', '+OR+', $search_terms);
    $search_phrase = str_replace(' ', '+', $search_phrase);
    $exclude_terms = str_replace(' ', '+', $exclude_terms);
    
    //SimpleXML load results feed
    //libxml_use_internal_errors(true);
    
    $twitter_results = simplexml_load_file(WP_CONTENT_URL.'/plugins/wp-twittersearch/search-feed.php?q='.$search_terms.'&phrase='.$search_phrase.'&nots='.$exclude_terms.'&from='.$search_person.'&lang='.$search_lang.'&rpp='.$limit_tweets);
    
    //print_r($twitter_results->entry);
            
    // Loop the resulting Twitter Search data
    if (!$twitter_results) {
      $errors = libxml_get_errors(); ?>

        <p>There was a problem retrieving tweets from Twitter. Please refresh to try again.</p>

      <?php libxml_clear_errors();
    } else {
      foreach($twitter_results->entry as $tweet){
            
        preg_match('/([a-z0-9]+) \(([a-z0-9\s]+)\)/i', $tweet->author->name, $names);
        list($original_names, $twitter_name, $display_name) = $names;
        //echo $twitter_name . '-' . $display_name;
        $tweet_date = date(get_option('wptwittersearch_dateformat'), strtotime($tweet->published));
    	
      	echo '<div class="tweet">';
      		if (get_option('wptwittersearch_avatar') == '1') {
      		  echo '<div class="avatar_border"><a href="'.$tweet->author->uri.'"><img style="width:48px;height:48px;" class="avatar" src="'.$tweet->link[1]['href'].'" /></a></div>';
      		}

      		if (get_option('wptwittersearch_name') == '1') {
      		  echo '<h4><a href="'.$tweet->author->uri.'">'.$display_name.'</a></h4>';
      		} else {
      		  echo '<h4>@<a href="'.$tweet->author->uri.'">'.$twitter_name.'</a></h4>';
      		}

      		echo '<p>'.$tweet->content;
      		if (get_option('wptwittersearch_date') == '1') {
      		  echo ' <span class="tweet_date"><a href="'.$tweet->link[0]['href'].'">'.$tweet_date.'</a></span>';
      		}
      		echo '</p>';
      	echo '</div>';
      }
    } //if results
    //share the love man...
    if (get_option('wptwittersearch_linklove') == '1') {
      echo '<p class="wpts-linklove">Powered by <a href="' . WPTS_PLUGIN_URL .'">WP-TwitterSearch</a></p>';
    }
  } //wp_twittersearch_feed
  
} //WPTwitterSearch

$wpTwitterSearch = new WPTwitterSearch;
?>