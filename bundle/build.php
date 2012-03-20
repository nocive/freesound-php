#!/usr/bin/php -q
<?php

// where to write bundle?
$file = __DIR__ . '/Freesound.php';



require_once( __DIR__ . '/../lib/Freesound.php' );

echo "Writing bundle file to '$file'... ";

$status = false;
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
