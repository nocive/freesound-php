<?php

require_once( 'config.php' );

//$r = $fs->SoundSearch( 'foo' );
$r = $fs->Sound()->Search( 'foo' );
var_dump( $r );

?>
