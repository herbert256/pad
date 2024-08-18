<?php

  if     ( strpos ( $padParm, '..' ) ) return include '/pad/sequence/inits/sequence/range.php';
  elseif ( strpos ( $padParm, ';'  ) ) return include '/pad/sequence/inits/sequence/list.php';
  elseif ( ctype_digit ( $padParm  ) ) return include '/pad/sequence/inits/sequence/digit.php';

  $padSeqSeq  = array_key_first ( $padPrm [$pad] ) ?? '';
  $padSeqParm = $padPrm [$pad] [$padSeqSeq]        ?? '';

  if ( isset ( $padSeqStore [$padSeqSeq] ) ) 
    return include '/pad/sequence/inits/sequence/store.php';

  if ( file_exists ( "/pad/sequence/types/$padSeqSeq" ) ) 
    return include '/pad/sequence/inits/sequence/type.php';
 
  if ( isset ( $padSeqRows ) or isset ( $padSeqTo ) )
    return include '/pad/sequence/inits/sequence/loop.php';

  padError ( "Sequence not found: $padSeqSeq ");

?>