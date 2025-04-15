<?php

  if ( $padSeqPrefix and isset ( $padSeqStore [$padSeqPrefix] ) and ! in_array ( $padSeqPrefix, $padSeqDone ) ) {
    $padSeqSeq = $padSeqPrefix;
    return 'prefix-store'; 
  }

  if ( $padSeqPrefix and file_exists ( "sequence/types/$padSeqPrefix" ) and ! in_array ( $padSeqPrefix, $padSeqDone ) ) {
    $padSeqSeq = $padSeqPrefix;
    return 'prefix-type'; 
  }

  return FALSE;
    
?>