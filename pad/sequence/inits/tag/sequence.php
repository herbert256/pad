<?php

  $padSeqInfo ['kinds'] [] = include 'sequence/inits/sequence/info.php';

  $padTmp = $padSeqParm1;

  if ( $padTmp )
    if     ( is_numeric ( $padTmp              )      ) return include 'sequence/inits/sequence/integer.php';
    elseif ( strpos     ( $padTmp, '..'        )      ) return include 'sequence/inits/sequence/range.php';
    elseif ( strpos     ( $padTmp, ';'         )      ) return include 'sequence/inits/sequence/list.php';
    elseif ( file_exists ( "sequence/types/$padTmp" ) ) return include 'sequence/inits/sequence/parm-type.php';
    elseif ( isset ( $padSeqStore [$padTmp]    )      ) return include 'sequence/inits/sequence/parm-store.php';
 
  foreach ( $padOptionsSingle as $padSeqSeq => $padSeqParm )  
    if     ( file_exists ( "sequence/types/$padSeqSeq" ) ) return include 'sequence/inits/sequence/option-type.php';
    elseif ( isset ( $padSeqStore [$padSeqSeq] )    )      return include 'sequence/inits/sequence/option-store.php';
 
  return include 'sequence/inits/sequence/default.php';

    
?>