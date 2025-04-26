<?php

  foreach ( $padPrm [$pad] as $padK => $padV )
        if ( file_exists ( "sequence/options/types/$padK.php" ) ) padDone ( $padK );
    elseif ( file_exists ( "sequence/actions/types/$padK.php" ) ) padDone ( $padK );
    elseif ( file_exists ( "sequence/types/$padK" )             ) padDone ( $padK );

?>