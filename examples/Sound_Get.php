<?php

require_once( 'config.php' );

//$r = $fs->SoundGet( 120597 );
//$r = $fs->Sound()->Get( 120597 );
$r = $fs->Sound( 120597 )->Get();
var_dump( $r );

?>
