<?php

  // Claims the tag parameters the sequence subsystem has already dealt with: every parameter
  // whose name matches a sequence option (pqOption), an action (PA) or a sequence type
  // (PT) is marked with padDone, so the generic PAD option walker in options/_go/options.php
  // does not run a same-named engine option over the output as well.
  //
  // Reads $padPrm[$pad], writes $padDone[$pad].

  if ( $pqEntry != 'tag' )
    return;

  foreach ( $padPrm [$pad] as $padK => $padV )
        if ( pqOption ( $padK, $padV ) ) padDone ( $padK );
    elseif ( pqAction ( $padK )        ) padDone ( $padK );
    elseif ( pqSeq    ( $padK )        ) padDone ( $padK );

?>
