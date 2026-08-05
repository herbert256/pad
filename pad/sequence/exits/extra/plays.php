<?php

  // Extra columns: merges the per-term play results into the rows. $pqPlaysHit holds, for
  // each accepted term, the value every {make}/{keep}/{remove}/{flag} play returned, keyed
  // by the play's sequence name (built by plays/plays.php, collected by build/one.php).

  foreach ( $padData [$pad] as $padK => $padV )
    if ( isset ( $pqPlaysHit [$padK] ) )
      $padData [$pad] [$padK] = array_merge ( $padV, $pqPlaysHit [$padK] );

?>