<?php

  if     ( $padType [$pad] == 'seq'   )  return include 'seq/inits/short/seq.php';
  elseif ( $padType [$pad] == 'store' )  return include 'seq/inits/short/store.php';

  if     ( is_numeric ( $padParm       ) ) return include 'seq/inits/seq/integer.php';
  elseif ( strpos     ( $padParm, '..' ) ) return include 'seq/inits/seq/range.php';
  elseif ( strpos     ( $padParm, ';'  ) ) return include 'seq/inits/seq/list.php';
 
  foreach ( $padOptionsSingle as $padSeqSeq => $padSeqParm )  
    if ( file_exists ( "seq/types/$padSeqSeq" ) ) 
      return include 'seq/inits/seq/seq.php';
    elseif ( isset ( $padSeqStore [$padSeqSeq] ) )          
      return include 'seq/inits/seq/store.php';
 
  return include 'seq/inits/seq/default.php';
    
?>