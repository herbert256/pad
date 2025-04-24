<?php

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

        if ( in_array ( $padPrmName, $pqDone ) ) continue;
    elseif ( $padPrmKind <> 'option'           ) continue;

    if ( pqPlay ( $padPrmName ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $pqPlay = $padPrmName;
      padSplit ( '|', $padPrmValue, $padPrmName, $padPrmValue );
      include 'sequence/plays/add.php';     
      continue;
    }

    if ( pqPlay ( $padPrmName ) ) {
      $pqPlay = $padPrmName;
      continue;
    }

    if ( ! pqSeq ( $padPrmName ) )
      continue;

    include 'sequence/plays/add.php';

  }

?>