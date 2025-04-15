<?php

  if ( isset ( $padSeqStore [$padSeqTag] ) and ! in_array ( $padSeqTag, $padSeqDone ) ) {
    $padSeqSeq = $padSeqTag;
    return 'tag-store'; 
  }

  if ( file_exists ( "sequence/types/$padSeqTag" ) and ! in_array ( $padSeqTag, $padSeqDone ) ) {
    $padSeqSeq = $padSeqTag;
    return 'tag-type'; 
  }

  return FALSE;
    
?>