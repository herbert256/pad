<?php

  $padSeqAction = $padSeqSeq = '';

      if ( $padSeqPrefix and file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) ) $padSeqAction = $padSeqPrefix;
  elseif ( $padSeqTag    and file_exists ( "sequence/actions/types/$padSeqTag.php"    ) ) $padSeqAction = $padSeqTag;
  elseif ( $padSeqPrefix and file_exists ( "sequence/types/$padSeqPrefix"             ) ) $padSeqSeq    = $padSeqPrefix;  
  elseif ( $padSeqTag    and file_exists ( "sequence/types/$padSeqTag"                ) ) $padSeqSeq    = $padSeqTag;

      if ( $padSeqAction ) include 'sequence/inits/type/pull/action.php';
  elseif ( $padSeqSeq    ) include 'sequence/inits/type/pull/sequence.php';

?>