<?php

  $padSeqSeq  = 'loop';
  $padSeqSet  = 'sequence';
  $padSeqParm = TRUE;

  $padSeqChk = [ $padTag [$pad], $padOpt [$pad] [1], array_key_first($padPrm [$pad]) ?? '', $padPrm [$pad]['type'] ?? '' ];
     
  foreach ( $padSeqChk as $padSeqTmp )
    if ( $padSeqTmp )
      if     ( isset($padSeqStore [$padSeqTmp])                      ) return include 'sequence/store.php';
      elseif ( padExists ( PAD . "sequence/types/$padSeqTmp" )       ) return include 'sequence/type.php';
      elseif ( padExists ( PAD . "sequence/actions/$padSeqTmp.php" ) ) return include 'sequence/action.php';
      elseif ( strpos($padSeqTmp, '..')                              ) return include 'sequence/range.php';
  
  if ( ctype_digit($padOpt [$pad] [1]) ) {
    $padSeqSeq = 'range';
    $padSeqParm = "1.." . $padOpt [$pad] [1];
  } 

?>