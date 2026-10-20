<?php

  // Extra columns for a chained sequence: when this run pulled a store, copies the fields the
  // run that filled that store left in $padSeqData[$pqPull] into the matching rows here, so
  // the org/play/action columns of the whole chain stay visible - each row those of the term
  // it is ($pqRowAt). Fields this run already set win, so the newest values are never
  // overwritten by the replayed ones.

  if ( ! $pqPull                ) return;
  if ( $pqPulledRows === NULL ) return;

  // The rows are those of the store position of each row's term, $pqResultAt - read by the
  // term's position among the results, a pull that left terms out (keep, remove, from=,
  // increment=) gave its rows the columns of the store's first terms. They are the rows as the
  // run pulled them, $pqPulledRows (actions/inits.php), not as an expression's shift of the
  // same store during the build left them.

  foreach ( $padData [$pad] as $padK1 => $padV1 ) {

    $pqChainAt = ( $pqRowAt [$padK1] !== FALSE ) ? ( $pqResultAt [ $pqRowAt [$padK1] ] ?? NULL ) : NULL;

    if ( $pqChainAt !== NULL and isset ( $pqPulledRows [$pqChainAt] ) )
      foreach ( $pqPulledRows [$pqChainAt] as $padK2 => $padV2 )
        if ( ! isset ( $padData [$pad] [$padK1] [$padK2] ) )
          $padData [$pad] [$padK1] [$padK2] = $padV2;

  }

?>
