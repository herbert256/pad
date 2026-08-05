<?php

  // Extra columns for a chained sequence: when this run pulled a store, copies the fields the
  // run that filled that store left in $padSeqData[$pqPull] into the matching rows here, so
  // the org/play/action columns of the whole chain stay visible. Fields this run already set
  // win, so the newest values are never overwritten by the replayed ones.

  if ( ! $pqPull                         ) return;
  if ( ! isset ( $padSeqData [$pqPull] ) ) return;

  foreach ( $padData [$pad] as $padK1 => $padV1 )
    if ( isset ( $padSeqData [$pqPull] [$padK1] ) )
      foreach ( $padSeqData [$pqPull] [$padK1] as $padK2 => $padV2 )
        if ( ! isset ( $padData [$pad] [$padK1] [$padK2] ) )
          $padData [$pad] [$padK1] [$padK2] = $padV2;

?>