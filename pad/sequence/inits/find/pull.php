<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( $padPrmKind <> 'option' or in_array ( $padPrmName, $padSeqDone ) )
      continue;

    if ( isset ( $padSeqStore [$padPrmName] ) ) {

      $padSeqDone [] = $padPrmName; 
      $padSeqPull    = $padPrmName;

      return; 
    }

  }

?>