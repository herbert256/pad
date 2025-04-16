<?php

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

        if ( in_array ( $padPrmName, $padSeqDone ) ) continue;
    elseif ( $padPrmKind <> 'option'               ) continue;

    if ( padSeqPlay ( $padPrmName ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $padSeqPlay = $padPrmName;
      padSplit ( '|', $padPrmValue, $padPrmName, $padPrmValue );
      include 'sequence/plays/add.php';     
      continue;
    }

    if ( padSeqPlay ( $padPrmName ) ) {
      $padSeqPlay = $padPrmName;
      continue;
    }

    if ( ! file_exists ( "sequence/types/$padPrmName" ) )
      continue;

    include 'sequence/plays/add.php';

  }

?>