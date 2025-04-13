<?php

  $padSeqPlay = 'make';

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

        if ( in_array ( $padPrmName, $padSeqDone ) ) continue;
    elseif ( $padPrmName == $padSeqSeqSave         ) continue;
    elseif ( $padPrmKind <> 'option'               ) continue;

    if ( padSeqPlay ( $padPrmName ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $padSeqPlay = $padPrmName;
      padSplit ( '|', $padPrmValue, $padSeqSeq, $padPrmValue );
      include 'sequence/plays/add.php';     
      continue;
    }

    if ( padSeqPlay ( $padPrmName ) ) {
      $padSeqPlay = $padPrmName;
      continue;
    }

    if ( ! file_exists ( "sequence/types/$padPrmName" ) )
      continue;

    $padSeqSeq = $padPrmName;

    include 'sequence/plays/add.php';

  }

?>