<?php

  if ( $padSeqParm )
    if     ( is_numeric ( $padSeqParm              )      ) return 'integer';
    elseif ( strpos     ( $padSeqParm, '..'        )      ) return 'range';
    elseif ( strpos     ( $padSeqParm, ';'         )      ) return 'list';
   
  foreach ( $padParms [$pad] as $padParmsOne )  {

    if ( $padParmsOne ['padPrmKind'] == 'option' ) {

      $padSeqSeq   = $padParmsOne ['padPrmName']; 
      $padSeqParm  = $padParmsOne ['padPrmValue'];

      if     ( file_exists ( "sequence/types/$padSeqSeq" ) ) return 'type';
      elseif ( isset ( $padSeqStore [$padSeqSeq] )         ) return 'store';

    }

  }

 
  return 'default';
    
?>