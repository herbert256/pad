<?php

  if ( ! $pqPull                         ) return;
  if ( ! isset ( $padSeqData [$pqPull] ) ) return;

  foreach ( $padData [$pad] as $padK1 => $padV1 )
    if ( isset ( $padSeqData [$pqPull] [$padK1] ) )
      foreach ( $padSeqData [$pqPull] [$padK1] as $padK2 => $padV2 )
        if ( ! isset ( $padData [$pad] [$padK1] [$padK2] ) )
          $padData [$pad] [$padK1] [$padK2] = $padV2;

?>
