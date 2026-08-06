<?php

  // Claims the tag parameters the sequence subsystem has already dealt with: every parameter
  // whose name matches a sequence option (options/types/), an action (PA) or a sequence type
  // (PT) is marked with padDone, so the generic PAD option walker in options/go/options.php
  // does not run a same-named engine option over the output as well.
  //
  // Reads $padPrm[$pad], writes $padDone[$pad].

  foreach ( $padPrm [$pad] as $padK => $padV )
        if ( file_exists ( PQ . "options/types/$padK.php" ) ) padDone ( $padK );
    elseif ( file_exists ( PA . "$padK.php"               ) ) padDone ( $padK );
    elseif ( file_exists ( PT . "$padK"                   ) ) padDone ( $padK );

?>