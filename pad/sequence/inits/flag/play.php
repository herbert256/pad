<?php

  foreach ( $padSeqPlays as $padK => $padV )

    if ( $padV ['padSeqPlay'] == 'make' ) {

      $padSeqPlays [$padK] ['padSeqBuild'] = 'check';
      $padSeqPlays [$padK] ['padSeqPlay']  = 'flag';

      return;

    }

  $padSeqParmSave = $padSeqParm;
  $padSeqSeqSave  = $padSeqSeq;

  aaaa ();
  
  $padSeqParm = $padSeqParmSave;
  $padSeqSeq  = $padSeqSeqSave;

?>