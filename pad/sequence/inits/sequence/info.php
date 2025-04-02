<?php

  if ( $padSeqParm )
    if     ( is_numeric ( $padSeqParm              )      ) return 'integer';
    elseif ( strpos     ( $padSeqParm, '..'        )      ) return 'range';
    elseif ( strpos     ( $padSeqParm, ';'         )      ) return 'list';
   
  foreach ( $padParms [$pad] as $padParmsOne )  {

    if ( $padParmsOne ['padPrmKind'] == 'option' ) {

      $padSeqTmp = $padParmsOne ['padPrmName']; 

      if     ( file_exists ( "sequence/types/$padSeqTmp" ) ) return 'type';
      elseif ( isset ( $padSeqStore [$padSeqTmp] )         ) return 'store';

    }

  }

 
  return 'default';
    
?>