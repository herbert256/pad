<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    $padSeqSeq  = $padParmsOne ['padPrmName']; 
    $padSeqParm = $padParmsOne ['padPrmValue'];

    if ( in_array ( $padSeqSeq, $padSeqDone ) )
      continue;

    if ( $padParmsOne ['padPrmKind'] == 'option' and isset ( $padSeqStore [$padSeqSeq] ) ) 
      return 'store'; 

  }

  return FALSE;
    
?>