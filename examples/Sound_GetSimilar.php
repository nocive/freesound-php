
<?php

require_once( 'config.php' );

//$r = $fs->SoundGetSimilar( 120597 );
//$r = $fs->Sound()->GetSimilar( 120597 );
$r = $fs->Sound( 120597 )->GetSimilar( 20 );
var_dump( $r );

?>
