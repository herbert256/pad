<?php

  if     ( strpos ( $padSeqEntryParm, '..' ) ) return include '/pad/sequence/inits/sequence/range.php';
  elseif ( strpos ( $padSeqEntryParm, ';'  ) ) return include '/pad/sequence/inits/sequence/list.php';

  foreach ( $padSeqEntryList as $padSeqSeq => $padSeqParm ) {

    if     ( strpos ( $padSeqSeq, '..' ) ) return include '/pad/sequence/inits/sequence/range.php';
    elseif ( strpos ( $padSeqSeq, ';'  ) ) return include '/pad/sequence/inits/sequence/list.php';

    if ( $padSeqSeq and isset ( $padSeqStore [$padSeqSeq] ) ) 
      return include '/pad/sequence/inits/sequence/store.php';

    if ( $padSeqSeq and file_exists ( "/pad/sequence/types/$padSeqSeq" ) ) 
      return include '/pad/sequence/inits/sequence/type.php';

  }

  return include '/pad/sequence/inits/sequence/loop.php';
 
?>