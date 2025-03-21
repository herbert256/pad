<?php

  if     ( is_numeric ( $padParm              ) ) return include 'seq/inits/tag/sequence/integer.php';
  elseif ( strpos     ( $padParm, '..'        ) ) return include 'seq/inits/tag/sequence/range.php';
  elseif ( strpos     ( $padParm, ';'         ) ) return include 'seq/inits/tag/sequence/list.php';
  elseif ( file_exists ( "seq/types/$padParm" ) ) return include 'seq/inits/tag/sequence/parm-type.php';
  elseif ( isset ( $padSeqStore [$padParm]    ) ) return include 'seq/inits/tag/sequence/parm-store.php';
 
  foreach ( $padOptionsSingle as $padSeqSeq => $padSeqParm )  
    if     ( file_exists ( "seq/types/$padSeqSeq" ) ) return include 'seq/inits/tag/sequence/type.php';
    elseif ( isset ( $padSeqStore [$padSeqSeq] )    ) return include 'seq/inits/tag/sequence/store.php';
 
  return include 'seq/inits/tag/sequence/default.php';
    
?>