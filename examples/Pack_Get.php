<?php

require_once( 'config.php' );

//$r = $fs->PackGet( 5107 );
//$r = $fs->Pack()->Get( 5107 );
$r = $fs->Pack( 5107 )->Get();
var_dump( $r );

?>
