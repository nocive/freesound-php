<?php

require_once( 'config.php' );

//$r = $fs->PackGetSounds( 5107 );
//$r = $fs->Pack()->GetSounds( 5107 );
$r = $fs->Pack( 5107 )->GetSounds();
var_dump( $r );

?>
