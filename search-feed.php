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

$phrase = $_GET['phrase'];
$from   = $_GET['from'];
$lang   = $_GET['lang'];
$nots   = $_GET['nots'];
$rpp    = $_GET['rpp'];

// Search Twitter API Request
$response = $twitter->searchAPI('ATOM');
echo $feed = $response->search($q,array('phrase'=>$phrase,'from'=>$from,'rpp'=>$rpp,'lang'=>$lang,'nots'=>$nots));

?>