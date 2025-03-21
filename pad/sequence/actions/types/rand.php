<?php

  $padSeqActionTmp = array_flip ( $padSeqResult );

  if ( count($padSeqResult) < $padSeqActionCnt )
    return array_rand ( $padSeqActionTmp, count($padSeqResult) );
  else
    return array_rand ( $padSeqActionTmp, $padSeqActionCnt );
  
?>