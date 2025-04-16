<?php

  if     ( isset ( $padSeqStore [$padSeqTag]                     ) ) $padSeqPull   = $padSeqTag;
  elseif ( file_exists ( "sequence/types/$padSeqTag"             ) ) $padSeqSeq    = $padSeqTag;
  elseif ( file_exists ( "sequence/actions/types/$padSeqTag.php" ) ) $padSeqAction = $padSeqTag;

?>