<?php

  if ( isset ( $padPrm [$pad] ['random'] ) )
    $padSeqLoop = padSeqRandomLoop ( $padSeqFor, $padSeqLoopStart, $padSeqLoopEnd, $padSeqInc );

  if ( $padSeqBuild == 'fixed' ) 

    $padSequence = $padSeqLoop;

  elseif ( $padSeqBuild == 'function' )

    $padSequence = ( "padSeq" . ucfirst($padSeqSeq) ) ($padSeqLoop);

  elseif ( $padSeqBuild == 'bool' )

    $padSequence = include pad . "sequence/type/go/bool.php";

  else

    $padSequence = include "$padSeqType/$padSeqBuild.php";

  if     ( $padSequence === NULL  ) return FALSE;
  elseif ( $padSequence === FALSE ) return TRUE;   
  elseif ( $padSequence === INF   ) return FALSE; 
  elseif ( $padSequence === NAN   ) return FALSE; 
  elseif ( $padSequence === TRUE  ) return $padSeqLoop;
  else                              return $padSequence;

?>