<?php

require_once( 'config.php' );

//$r = $fs->UserGetPacks( 'antigonia' );
//$r = $fs->User()->GetPacks( 'antigonia' );
$r = $fs->User( 'antigonia' )->GetPacks();
var_dump( $r );

?>
