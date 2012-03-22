<?php

require_once( __DIR__ . '/../lib/Freesound.php' );
//require_once( __DIR__ . '/../bundle/Freesound.php' );

$fs = new Freesound( '77bbf1a63bc84ccc9d80a38d6345ef60' );
$fs->Config( 'debug', 1 );

$params = array(
	'SoundSearch' => array( 'foo' ),
	'SoundGet' => array( 120597 ),
	'SoundSearchGeo' => array( 41.3265528618605, 41.4504467428547, 2.005176544189453, 2.334766387939453 ),
	'SoundGetAnalysis' => array( 120597 ),
	'SoundGetSimilar' => array( 120597 ),
	'UserGet' => array( 'antigonia'  ),
	'UserGetSounds' => array( 'antigonia' ),
	'UserGetPacks' => array( 'antigonia' ),
	'UserGetBookmarkCategories' => array( 'antigonia' ),
	//'UserGetBookmarkCategorySounds' => array( 'antigonia', 2127 ),
	'UserGetBookmarkCategorySounds' => array( 'antigonia' ),
	'PackGet' => array( 5107 ),
	'PackGetSounds' => array( 5107 )
);

$test = new Freesound_Test( $fs, $params );
$r = $test->RunAll();

//var_dump( $r );

?>
