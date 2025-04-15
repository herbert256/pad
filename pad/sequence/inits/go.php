<?php

  if ( $padSeqContinue )
    return include 'sequence/continue/go.php';

  if ( $padSeqType <> 'pad' ) include "sequence/inits/type/go.php";
  else                        include "sequence/inits/tag/go.php";

  $padSeqSeqInit = $padSeqSeq;

  if     ( padSeqStore ( $padSeqSeq )                                 ) return;
  elseif ( $padSeqSeq and file_exists ( "sequence/types/$padSeqSeq" ) ) return;

  include "sequence/inits/seqseq.php";
  
?>