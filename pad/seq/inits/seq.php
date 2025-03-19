<?php

  if     ( $padType [$pad] == 'seq'    )  return include 'seq/inits/short/types/seq.php';
  elseif ( $padType [$pad] == 'store'  )  return include 'seq/inits/short/types/store.php';
  elseif ( $padType [$pad] == 'one'    )  return include 'seq/inits/short/types/one.php';
  elseif ( $padType [$pad] == 'action' )  return include 'seq/inits/short/types/action.php';
  elseif ( $padType [$pad] == 'make'   )  return include 'seq/inits/short/types/make.php';
  elseif ( $padType [$pad] == 'keep'   )  return include 'seq/inits/short/types/keep.php';
  elseif ( $padType [$pad] == 'remove' )  return include 'seq/inits/short/types/remove.php';

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