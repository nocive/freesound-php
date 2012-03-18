<?php

require_once( 'config.php' );

//$r = $fs->SoundSearch( 'foo' );
$r = $fs->Sound()->Search( 'foo' );

//$r = $fs->SoundGet( 120597 );
//$r = $fs->Sound()->Get( 120597 );
$r = $fs->Sound( 120597 )->Get();

//$r = $fs->SoundSearchGeo( 41.3265528618605, 41.4504467428547, 2.005176544189453, 2.334766387939453 );
$r = $fs->Sound()->SearchGeo( 41.3265528618605, 41.4504467428547, 2.005176544189453, 2.334766387939453 );

//$r = $fs->SoundGetAnalysis( 120597 );
//$r = $fs->Sound()->GetAnalysis( 120597 );
$r = $fs->Sound( 120597 )->GetAnalysis();

//$r = $fs->SoundGetSimilar( 120597 );
//$r = $fs->Sound()->GetSimilar( 120597 );
$r = $fs->Sound( 120597 )->GetSimilar();

//$r = $fs->UserGet( 'antigonia' );
//$r = $fs->User()->Get( 'antigonia' );
$r = $fs->User( 'antigonia' )->Get();

//$r = $fs->UserGetSounds( 'antigonia' );
//$r = $fs->User()->GetSounds( 'antigonia' );
$r = $fs->User( 'antigonia' )->GetSounds();

//$r = $fs->UserGetPacks( 'antigonia' );
//$r = $fs->User()->GetPacks( 'antigonia' );
$r = $fs->User( 'antigonia' )->GetPacks();

//$r = $fs->PackGet( 5107 );
//$r = $fs->Pack()->Get( 5107 );
$r = $fs->Pack( 5107 )->Get();

//$r = $fs->PackGetSounds( 5107 );
//$r = $fs->Pack()->GetSounds( 5107 );
$r = $fs->Pack( 5107 )->GetSounds();

//var_dump( $r );

?>
