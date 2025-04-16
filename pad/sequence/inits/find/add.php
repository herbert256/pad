<?php

  $padPrmName  = $padSeqSeq;
  $padPrmValue = $padSeqParm;
  
  if     ( padSeqPlay ( $padSeqTag )           ) $padSeqPlay = $padSeqTag;
  elseif ( padSeqPlay ( $padSeqType )          ) $padSeqPlay = $padSeqType;
  else                                           $padSeqPlay = 'make';

  include 'sequence/plays/add.php';

  $padSeqSeq  = '';
  $padSeqParm = '';

?>