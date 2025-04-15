<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    $padSeqSeq  = $padParmsOne ['padPrmName']; 
    $padSeqParm = $padParmsOne ['padPrmValue'];

    if ( $padParmsOne ['padPrmKind'] == 'option' ) {  

      if ( $padSeqSeq == 'from'      ) return 'from';
      if ( $padSeqSeq == 'to'        ) return 'to';
      if ( $padSeqSeq == 'rows'      ) return 'rows';
      if ( $padSeqSeq == 'increment' ) return 'increment';

    }

  }

  return FALSE;
    
?>