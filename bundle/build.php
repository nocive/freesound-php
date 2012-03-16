#!/usr/bin/php -q
<?php

require_once( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Freesound.php' );


$file = __DIR__ . DIRECTORY_SEPARATOR . 'Freesound.php';
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
