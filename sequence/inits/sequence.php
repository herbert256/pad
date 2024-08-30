<?php

  if     ( strpos ( $padParm, '..' ) ) return include '/pad/sequence/inits/sequence/range.php';
  elseif ( strpos ( $padParm, ';'  ) ) return include '/pad/sequence/inits/sequence/list.php';

  foreach ( $padPrm [$pad] as $padSeqSeq => $padSeqParm ) {

    if ( $padSeqSeq and isset ( $padSeqStore [$padSeqSeq] ) ) 
      return include '/pad/sequence/inits/sequence/store.php';

    if ( $padSeqSeq and file_exists ( "/pad/sequence/types/$padSeqSeq" ) ) 
      return include '/pad/sequence/inits/sequence/type.php';

  }

  return include '/pad/sequence/inits/sequence/loop.php';
 
?>