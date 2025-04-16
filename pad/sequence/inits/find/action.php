<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( $padPrmKind <> 'option' or in_array ( $padPrmName, $padSeqDone ) )
      continue;

    if ( file_exists ( "sequence/actions/types/$padPrmName.php" ) ) {

      $padSeqDone []    = $padPrmName; 
      $padSeqAction = $padPrmName;
      $padSeqActionParm = $padPrmValue;

      return; 

    }

  }

?>