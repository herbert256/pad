<?php

  $padSeqPlay = '';

  if ( $padSeqPrefix and padSeqPlay ( $padSeqPrefix ) and file_exists ( "sequence/types/$padSeqTag") ) {
    $padSeqPlay  = $padSeqPrefix;
    $padSeqSeq   = $padSeqTag;
    $padPrmValue = $padSeqParm;
  } elseif ( $padSeqPrefix and padSeqPlay( $padSeqTag ) and file_exists ( "sequence/types/$padSeqPrefix") ) {
    $padSeqPlay  = $padSeqTag;
    $padSeqSeq   = $padSeqPrefix;
    $padPrmValue = $padSeqParm;
  } elseif ( $padSeqFirst and padSeqPlay( $padSeqTag ) and file_exists ( "sequence/types/$padSeqFirst") ) {
    $padSeqPlay  = $padSeqTag;
    $padSeqSeq   = $padSeqFirst;
    $padPrmValue = $padSeqFirstParm;
  }

  if ( $padSeqPlay ) {
    $padSeqPlaySource = 'inits/play/check';
    include 'sequence/plays/add.php'; 
  }

?>