<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( $padPrmKind <> 'option' or in_array ( $padPrmName, $padSeqDone ) )
      continue;

    if ( file_exists ( "sequence/types/$padPrmName" ) ) {

      $padSeqDone [] = $padPrmName; 
      $padSeqSeq    = $padPrmName;
      $padSeqParm   = $padPrmValue;

      return; 

    }

  }

?>