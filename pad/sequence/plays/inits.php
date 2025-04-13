<?php

  $padSeqPlay = 'make';
  $padSeqLast = FALSE;

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

    if ( $padPrmKind == 'parm' and $padSeqLast !== FALSE ) 
      $padSeqPlays [$padSeqLast] ['padSeqParm'] = $padPrmValue;

        if ( in_array ( $padPrmName, $padSeqDone ) ) continue;
    elseif ( $padPrmName == $padSeqSeqSave         ) continue;
    elseif ( $padPrmKind <> 'option'               ) continue;

    if ( padSeqPlay ( $padPrmName ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $padSeqPlay = $padPrmName;
      padSplit ( '|', $padPrmValue, $padSeqSeq, $padPrmValue );
      $padSeqPlaySource = 'plays/inits1';
      include 'sequence/plays/add.php';     
      continue;
    }

    if ( padSeqPlay ( $padPrmName ) ) {
      $padSeqPlay = $padPrmName;
      continue;
    }

    $padSeqSeq = $padPrmName;

    $padSeqPlaySource = 'plays/inits2';
    include 'sequence/plays/add.php';

  }

?>