<?php

require_once( 'config.php' );

//$r = $fs->UserGet( 'antigonia' );
//$r = $fs->User()->Get( 'antigonia' );
$r = $fs->User( 'antigonia' )->Get();
var_dump( $r );

?>
