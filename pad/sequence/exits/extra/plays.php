<?php

  // Extra columns: merges the per-term play results into the rows. $pqPlaysHit holds, for
  // each accepted term, the value every {make}/{keep}/{remove}/{flag} play returned, keyed
  // by the play's sequence name (built by plays/plays.php, collected by build/one.php), each
  // row getting those of the term it is ($pqRowAt).

  foreach ( $padData [$pad] as $padK => $padV )
    if ( $pqRowAt [$padK] !== FALSE and isset ( $pqPlaysHit [ $pqRowAt [$padK] ] ) )
      $padData [$pad] [$padK] = array_merge ( $padV, $pqPlaysHit [ $pqRowAt [$padK] ] );

?>
