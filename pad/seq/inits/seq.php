<?php

  if     ( $padType [$pad] == 'seq'   )  return include PAD . 'seq/inits/short/seq.php';
  elseif ( $padType [$pad] == 'store' )  return include PAD . 'seq/inits/short/store.php';

  if     ( is_numeric ( $padParm       ) ) return include PAD . 'seq/inits/seq/integer.php';
  elseif ( strpos     ( $padParm, '..' ) ) return include PAD . 'seq/inits/seq/range.php';
  elseif ( strpos     ( $padParm, ';'  ) ) return include PAD . 'seq/inits/seq/list.php';
 
  foreach ( $padOptionsSingle as $padSeqSeq => $padSeqParm )  
    if ( file_exists ( PAD . "seq/types/$padSeqSeq" ) ) 
      return include PAD . 'seq/inits/seq/seq.php';
    elseif ( isset ( $padSeqStore [$padSeqSeq] ) )          
      return include PAD . 'seq/inits/seq/store.php';
 
  return include PAD . 'seq/inits/seq/default.php';
    
?>