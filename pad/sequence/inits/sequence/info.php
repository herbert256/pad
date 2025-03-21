<?php

  if ( $padParm )
    if     ( is_numeric ( $padParm              )      ) return 'integer';
    elseif ( strpos     ( $padParm, '..'        )      ) return 'range';
    elseif ( strpos     ( $padParm, ';'         )      ) return 'list';
    elseif ( file_exists ( "sequence/types/$padParm" ) ) return 'parm-type';
    elseif ( isset ( $padSeqStore [$padParm]    )      ) return 'parm-store';
   
  foreach ( $padOptionsSingle as $padSeqSeq => $padSeqParm )  
    if     ( file_exists ( "sequence/types/$padSeqSeq" ) ) return 'type';
    elseif ( isset ( $padSeqStore [$padSeqSeq] )    )      return 'store';
 
  return 'default';
    
?>