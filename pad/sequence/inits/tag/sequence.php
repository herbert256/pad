<?php

  if     ( is_numeric ( $padParm              ) ) return include 'sequence/inits/sequence/integer.php';
  elseif ( strpos     ( $padParm, '..'        ) ) return include 'sequence/inits/sequence/range.php';
  elseif ( strpos     ( $padParm, ';'         ) ) return include 'sequence/inits/sequence/list.php';
  elseif ( file_exists ( "sequence/types/$padParm" ) ) return include 'sequence/inits/sequence/parm-type.php';
  elseif ( isset ( $padSeqStore [$padParm]    ) ) return include 'sequence/inits/sequence/parm-store.php';
 
  foreach ( $padOptionsSingle as $padSeqSeq => $padSeqParm )  
    if     ( file_exists ( "sequence/types/$padSeqSeq" ) ) return include 'sequence/inits/sequence/type.php';
    elseif ( isset ( $padSeqStore [$padSeqSeq] )    ) return include 'sequence/inits/sequence/store.php';
 
  return include 'sequence/inits/sequence/default.php';
    
?>