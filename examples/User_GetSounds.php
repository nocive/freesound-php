<?php

require_once( 'config.php' );

//$r = $fs->UserGetSounds( 'antigonia' );
//$r = $fs->User()->GetSounds( 'antigonia' );
$r = $fs->User( 'antigonia' )->GetSounds();
var_dump( $r );

?>
