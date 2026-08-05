<?php

  // Puts the play mode $pqCheck on the run's first play, for runs that read from a store and
  // so have no generation step of their own to flag - {pull:mySeq, keep, prime} filters the
  // pulled values, it does not generate primes.
  //
  // The loop body returns on its first pass, so only the first play is ever touched.

  foreach ( $pqPlays as $padK => $padV ) {

    $pqPlays [$padK] ['pqBuild'] = pqBuild ( $padV ['pqSeq'], $pqCheck );
    $pqPlays [$padK] ['pqPlay']  = $pqCheck;

    return;

  }

?>