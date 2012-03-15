<?php

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Freesound.php' );

//$fs = new Freesound();
$fs = new Freesound( '77bbf1a63bc84ccc9d80a38d6345ef60' );

/*$fs->config( array(
	'api_key' => '77bbf1a63bc84ccc9d80a38d6345ef60', 
	'fetch_connect_timeout' => 30, 
	'fetch_timeout' => 20, 
	'debug' => 1,
	'json_decode_assoc' => false
));*/

$fs->config( 'debug', 1 );


//$r = $fs->SoundSearch( 'foo' );
$r = $fs->Sound()->Search( 'foo' );

//$r = $fs->SoundGet( 120597 );
$r = $fs->Sound()->Get( 120597 );

//$r = $fs->SoundSearchGeo( 41.3265528618605, 41.4504467428547, 2.005176544189453, 2.334766387939453 );
$r = $fs->Sound()->SearchGeo( 41.3265528618605, 41.4504467428547, 2.005176544189453, 2.334766387939453 );

//$r = $fs->SoundGetAnalysis( 120597 );
$r = $fs->Sound()->GetAnalysis( 120597 );

//$r = $fs->SoundGetSimilar( 120597 );
$r = $fs->Sound()->GetSimilar( 120597 );

//$r = $fs->UserGet( 'artshare' );
$r = $fs->User()->Get( 'artshare' );

//$r = $fs->UserGetSounds( 'artshare' );
$r = $fs->User()->GetSounds( 'artshare' );

//$r = $fs->UserGetPacks( 'artshare' );
$r = $fs->User()->GetPacks( 'artshare' );

//$r = $fs->PackGet( 5107 );
$r = $fs->Pack()->Get( 5107 );

//$r = $fs->PackGetSounds( 5107 );
$r = $fs->Pack()->GetSounds( 5107 );

//var_dump( $r );

?>
