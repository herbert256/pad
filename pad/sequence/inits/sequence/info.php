<?php

  $padTmp = $padOpt [$pad] [1];

  if ( $padTmp )
    if     ( is_numeric ( $padTmp              )      ) return 'integer';
    elseif ( strpos     ( $padTmp, '..'        )      ) return 'range';
    elseif ( strpos     ( $padTmp, ';'         )      ) return 'list';
    elseif ( file_exists ( "sequence/types/$padTmp" ) ) return 'parm-type';
    elseif ( isset ( $padSeqStore [$padTmp]    )      ) return 'parm-store';
   
  foreach ( $padOptionsSingle as $padSeqSeq => $padSeqParm )  
    if     ( file_exists ( "sequence/types/$padSeqSeq" ) ) return 'option-type';
    elseif ( isset ( $padSeqStore [$padSeqSeq] )    )      return 'option-store';
 
  return 'default';
    
?>