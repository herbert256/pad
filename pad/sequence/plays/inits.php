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

    if ( in_array ( $padPrmName, ['make','keep','remove'] ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $padSeqPlay = $padPrmName;
      padSplit ( '|', $padPrmValue, $padSeqSeq, $padPrmValue );
      include 'sequence/plays/add.php';     
      continue;
    }

    if ( in_array ( $padPrmName, ['make','keep','remove'] ) ) {
      $padSeqPlay = $padPrmName;
      continue;
    }

    $padSeqSeq = $padPrmName;

    include 'sequence/plays/add.php';

  }

?>