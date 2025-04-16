<?php

  if ( $padSeqPrefix )
    if     ( isset ( $padSeqStore [$padSeqPrefix]                     ) ) $padSeqPull   = $padSeqPrefix;
    elseif ( file_exists ( "sequence/types/$padSeqPrefix"             ) ) $padSeqSeq    = $padSeqPrefix;
    elseif ( file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) ) $padSeqAction = $padSeqPrefix;

?>