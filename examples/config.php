<?php

require_once( __DIR__ . '/../lib/Freesound.php' );

$apiKey = '77bbf1a63bc84ccc9d80a38d6345ef60';

$fs = new Freesound( $apiKey );
$fs->Config( 'debug', 1 );
//$fs->Config( array(
//	'api_key' => $apiKey, 
//	'fetch_connect_timeout' => 30, 
//	'fetch_timeout' => 20, 
//	'debug' => 1,
//	'json_decode_assoc' => true
//));

?>
