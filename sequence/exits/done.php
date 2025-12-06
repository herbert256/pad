<?php

  foreach ( $padPrm [$pad] as $padK => $padV )
        if ( file_exists ( PAD . "sequence/options/types/$padK.php" ) ) padDone ( $padK );
    elseif ( file_exists ( PAD . "sequence/actions/types/$padK.php" ) ) padDone ( $padK );
    elseif ( file_exists ( PAD . "sequence/types/$padK" )             ) padDone ( $padK );

?>