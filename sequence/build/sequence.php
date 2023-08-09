<?php

  $padSeqSeq  = 'loop';
  $padSeqSet  = 'sequence';
  $padSeqParm = TRUE;
  $padSeqNext = pad . 'sequence/build/sequence';

  $padSeqChk = [ $padTag [$pad], 
                 $padOpt [$pad] [1], 
                 array_key_first($padPrm [$pad]) ?? '', 
                 $padPrm [$pad]['type'] ?? '' 
               ];
     
  foreach ( $padSeqChk as $padSeqTmp )
    if ( $padSeqTmp )
      if     ( isset($padSeqStore [$padSeqTmp])                      ) return include "$padSeqNext/store.php";
      elseif ( padExists ( "$padSeqTypes/$padSeqTmp" )       ) return include "$padSeqNext/type.php";
      elseif ( padExists ( pad . "sequence/actions/$padSeqTmp.php" ) ) return include "$padSeqNext/action.php";
      elseif ( strpos($padSeqTmp, '..')                              ) return include "$padSeqNext/range.php";
      elseif ( strpos($padSeqTmp, ';')                               ) return include "$padSeqNext/list.php";
  
  if ( ctype_digit($padOpt [$pad] [1]) ) {
    $padSeqSeq = 'range';
    $padSeqParm = "1.." . $padOpt [$pad] [1];
  } 

?>