<?php

  $padSeqSeq  = 'loop';
  $padSeqSet  = 'sequence';
  $padSeqParm = TRUE;

  $padSeqChk = [ $padTag [$pad], $padOpt [$pad] [1], array_key_first($padPrm [$pad]) ?? '', $padPrm [$pad]['type'] ?? '' ];
     
  foreach ( $padSeqChk as $padSeqTmp )
    if ( $padSeqTmp )
      if     ( isset($padSeqStore [$padSeqTmp])                      ) return include pad . 'sequence/build/sequence/store.php';
      elseif ( padExists ( pad . "sequence/types/$padSeqTmp" )       ) return include pad . 'sequence/build/sequence/type.php';
      elseif ( padExists ( pad . "sequence/actions/$padSeqTmp.php" ) ) return include pad . 'sequence/build/sequence/action.php';
      elseif ( strpos($padSeqTmp, '..')                              ) return include pad . 'sequence/build/sequence/range.php';
  
  if ( ctype_digit($padOpt [$pad] [1]) ) {
    $padSeqSeq = 'range';
    $padSeqParm = "1.." . $padOpt [$pad] [1];
  } 

?>