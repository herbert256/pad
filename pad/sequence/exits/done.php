<?php

  foreach ( $padPrm [$pad] as $padK => $padV )
        if ( file_exists ( PQ . "options/types/$padK.php" ) ) padDone ( $padK );
    elseif ( file_exists ( PQ . "actions/types/$padK.php" ) ) padDone ( $padK );
    elseif ( file_exists ( PT . "$padK" )             ) padDone ( $padK );

?>
