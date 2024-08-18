<?php

  if ( count ( $padPrm [$pad] ) ) {

    $padSeqChk = array_key_first ( $padPrm [$pad] );
    $padParm   = $padPrm [$pad] [$padSeqChk] ;

  } elseif ($padParm) {

    $padSeqChk = $padParm;

  } else {

    $padSeqChk = '*NONE*';

  }

  if     ( isset ( $padSeqStore [$padSeqChk] )               ) return include '/pad/seq/inits/seq/store.php';
  elseif ( file_exists ( "/pad/seq/types/$padSeqChk" )       ) return include '/pad/seq/inits/seq/type.php';
  elseif ( file_exists ( "/pad/seq/actions/$padSeqChk.php" ) ) return include '/pad/seq/inits/seq/action.php';
  elseif ( strpos ( $padSeqChk, '..' )                       ) return include '/pad/seq/inits/seq/range.php';
  elseif ( strpos ( $padSeqChk, ';' )                        ) return include '/pad/seq/inits/seq/list.php';
  elseif ( ctype_digit($padSeqChk)                           ) return include '/pad/seq/inits/seq/digit.php';
  else                                                         return include '/pad/seq/inits/seq/loop.php';

?>