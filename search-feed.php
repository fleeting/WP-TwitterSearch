<?php

// The PHP Library requires us to set the include path to the main 'lib' folder
set_include_path('lib/');

// Require the central PHP Library 'Twitter.php' file
require_once('lib/Arc90/Service/Twitter.php');

// Create a new instance of the Twitter object
$twitter = new Arc90_Service_Twitter();

// Conditionals for the get query from the url
if (isset($_GET['q']) && $_GET['q'] != '') {
	$q = $_GET['q'];
}

if (isset($_GET['phrase']) && $_GET['phrase'] != '') {
	$phrase = $_GET['phrase'];
}

if (isset($_GET['from']) && $_GET['from'] != '') {
	$from   = $_GET['from'];
}

if (isset($_GET['lang']) && $_GET['lang'] != '') {
	$lang   = $_GET['lang'];
}

if (isset($_GET['nots']) && $_GET['nots'] != '') {
	$nots   = $_GET['nots'];
}

if (isset($_GET['rpp']) && $_GET['rpp'] != '') {
	$rpp    = $_GET['rpp'];
}

// Search Twitter API Request
$response = $twitter->searchAPI('ATOM');
echo $feed = $response->search($q,array('phrase'=>$phrase,'from'=>$from,'rpp'=>$rpp,'lang'=>$lang,'nots'=>$nots));

?>