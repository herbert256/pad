<?php

  $padSeqFirstParmType = $padSeqFirstParmName = $padSeqFirstParmValue = '';

  foreach ( $padParms [$pad] as $padTmp)  {

    extract ( $padTmp );

    $padSeqFirstParmType  = $padPrmKind;
    $padSeqFirstParmName  = $padPrmName;
    $padSeqFirstParmValue = $padPrmValue;

    if ( $padSeqFirstParmType == 'soption' ) {

      if ( file_exists ( "sequence/types/$padSeqFirstParmName" ) ) {
        $padSeqSeq  = $padSeqFirstParmName; 
        $padSeqParm = $padSeqFirstParmValue;
        include 'sequence/inits/sequence/type.php';
      } elseif ( isset ( $padSeqStore [$padSeqFirstParmName] ) ) {
        $padSeqSeq  = $padSeqFirstParmName; 
        $padSeqParm = $padSeqFirstParmValue;
        include 'sequence/inits/sequence/store.php';
      }

    }

    return;

  }

?>