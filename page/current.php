<?php

  foreach ( get_defined_vars () as $padK => $padV )
    if ( padValidStore ($padK, 0, 3) and ! isset ( $padCurrent [$pad] [$padK] ) ) {
      $padCurrent [$pad] [$padK] = $padV;
      global $$padK;
    }

?>