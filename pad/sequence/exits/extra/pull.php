<?php

  // Extra column, only when this run pulled a store: gives each row a field named after the
  // store holding the value that sat at that position in $pqStore[$pqPull], so a template can
  // compare the replayed value with what the run made of it. 'n/a' where the store is shorter.

  if ( ! $pqPull )
    return;

  foreach ( $padData [$pad] as $padK => $padV )
    if ( isset ( $pqStore [$pqPull] [$padK] ) )
      $padData [$pad] [$padK] [$pqPull] = $pqStore [$pqPull] [$padK];
    else
      $padData [$pad] [$padK] [$pqPull] = 'n/a';

?>