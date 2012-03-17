<?php

require_once( __DIR__ . '/Freesound.php' );

$fs = new Freesound( '77bbf1a63bc84ccc9d80a38d6345ef60' );
$test = new Freesound_Test( $fs )
$test->runAll( $dumpResponses = false );

?>
