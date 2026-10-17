<?php

  // Extra columns for a chained sequence: when this run pulled a store, copies the fields the
  // run that filled that store left in $padSeqData[$pqPull] into the matching rows here, so
  // the org/play/action columns of the whole chain stay visible - each row those of the term
  // it is ($pqRowAt). Fields this run already set win, so the newest values are never
  // overwritten by the replayed ones.

  if ( ! $pqPull                         ) return;
  if ( ! isset ( $padSeqData [$pqPull] ) ) return;

  foreach ( $padData [$pad] as $padK1 => $padV1 )
    if ( $pqRowAt [$padK1] !== FALSE and isset ( $padSeqData [$pqPull] [ $pqRowAt [$padK1] ] ) )
      foreach ( $padSeqData [$pqPull] [ $pqRowAt [$padK1] ] as $padK2 => $padV2 )
        if ( ! isset ( $padData [$pad] [$padK1] [$padK2] ) )
          $padData [$pad] [$padK1] [$padK2] = $padV2;

?>
