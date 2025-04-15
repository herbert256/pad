<?php

  if     ( is_numeric ( $padSeqParm       ) ) return 'integer';
  elseif ( strpos     ( $padSeqParm, '..' ) ) return 'range';
  elseif ( strpos     ( $padSeqParm, ';'  ) ) return 'list';

  foreach ( $padParms [$pad] as $padParmsOne )  {

    $padSeqSeq  = $padParmsOne ['padPrmName']; 
    $padSeqParm = $padParmsOne ['padPrmValue'];

    if ( $padParmsOne ['padPrmKind'] == 'parm' ) {

      if     ( is_numeric ( $padSeqParm       ) ) return 'loop-integer';
      elseif ( strpos     ( $padSeqParm, '..' ) ) return 'loop-range';
      elseif ( strpos     ( $padSeqParm, ';'  ) ) return 'loop-list';

    } 
  
  }
  
  return FALSE;
    
?>