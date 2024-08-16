<?php

  $padSeqNext = '/pad/sequence/inits/sequence';

  $padSeqChk = [ $padTag [$pad], 
                 $padParm, 
                 array_key_first ( $padPrm [$pad] ) ?? ''
               ];
     
  foreach ( $padSeqChk as $padSeqTmp )
    if ( $padSeqTmp )
      if     ( isset ( $padSeqStore [$padSeqTmp] )                     ) return include "$padSeqNext/store.php";
      elseif ( file_exists ( "$padSeqTypes/$padSeqTmp" )               ) return include "$padSeqNext/type.php";
      elseif ( file_exists ( "/pad/sequence/actions/$padSeqTmp.php" ) ) return include "$padSeqNext/action.php";
      elseif ( strpos ( $padSeqTmp, '..' )                             ) return include "$padSeqNext/range.php";
      elseif ( strpos ( $padSeqTmp, ';' )                              ) return include "$padSeqNext/list.php";

  $padSeqSet = 'sequence';

  if ( ctype_digit($padParm) ) {
    $padSeqSeq = 'range';
    $padSeqParm = "1.." . $padParm;
  } else {
    $padSeqSeq  = 'loop';
    $padSeqParm = TRUE;
  }

?>