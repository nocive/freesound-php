#!/usr/bin/php -q
<?php

require_once( __DIR__ . '/../lib/Freesound.php' );


$file = __DIR__ . '/Freesound.php';
$status = false;

echo "Writing bundle file to '$file'... ";

try {
	$status = Freesound_Utils::BundleBuild( $file );
} catch (Exception $e) {
}

echo $status ? 'OK' : 'ERROR';
echo "\n";

if (isset( $e )) {
	echo 'Error details: ' . $e->getMessage() . "\n";
}

?>
