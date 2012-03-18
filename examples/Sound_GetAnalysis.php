<?php

require_once( 'config.php' );

//$r = $fs->SoundGetAnalysis( 120597 );
//$r = $fs->Sound()->GetAnalysis( 120597 );
$r = $fs->Sound( 120597 )->GetAnalysis();
var_dump( $r );

?>
