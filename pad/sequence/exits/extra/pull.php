<?php

  // Extra column, only when this run pulled a store: gives each row a field named after the
  // store holding the value that sat in $pqStore[$pqPull] at the position of the term the row
  // is ($pqRowAt), so a template can compare the replayed value with what the run made of it.
  // A row that is no term keeps the value it was given under that name; 'n/a' where there is
  // none, or the store is shorter.

  if ( ! $pqPull )
    return;

  foreach ( $padData [$pad] as $padK => $padV )
    if ( $pqRowAt [$padK] !== FALSE and isset ( $pqStore [$pqPull] [ $pqRowAt [$padK] ] ) )
      $padData [$pad] [$padK] [$pqPull] = $pqStore [$pqPull] [ $pqRowAt [$padK] ];
    elseif ( $pqRowAt [$padK] !== FALSE or ! isset ( $padV [$pqPull] ) )
      $padData [$pad] [$padK] [$pqPull] = 'n/a';

?>
