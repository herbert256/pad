<?php

  if ( $padSeqType <> 'pad' ) include "sequence/inits/type/go.php";
  else                        include "sequence/inits/tag/go.php";

  if     ( in_array ( $padSeqSeq, ['start','pull','store'] )          ) return;
  elseif ( $padSeqSeq and file_exists ( "sequence/types/$padSeqSeq" ) ) return;

  return include "sequence/inits/tag/sequence.php";
  
?>