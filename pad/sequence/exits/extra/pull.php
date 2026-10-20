<?php

  // Extra column, only when this run pulled a store: gives each row a field named after the
  // store holding the value that sat in $pqStore[$pqPull] at the position of the term the row
  // is ($pqRowAt), so a template can compare the replayed value with what the run made of it.
  // A row that is no term keeps the value it was given under that name; 'n/a' where there is
  // none, or the store is shorter.

  if ( ! $pqPull )
    return;

  // The value is read at the store position of the row's term ($pqResultAt) in the store as it
  // was pulled ($pqPulled): read at the term's position among the results, in the store as the
  // run left it, a keep or from= gave every row a value of the store's first entries, and after
  // a shift, a pop or push= the value of another term.

  foreach ( $padData [$pad] as $padK => $padV ) {

    $pqPullAt = ( $pqRowAt [$padK] !== FALSE ) ? ( $pqResultAt [ $pqRowAt [$padK] ] ?? NULL ) : NULL;

    if ( $pqPullAt !== NULL and isset ( $pqPulled [$pqPullAt] ) )
      $padData [$pad] [$padK] [$pqPull] = $pqPulled [$pqPullAt];
    elseif ( $pqRowAt [$padK] !== FALSE or ! isset ( $padV [$pqPull] ) )
      $padData [$pad] [$padK] [$pqPull] = 'n/a';

  }

?>
