<?php

  if ( count($padSeqResult) > $padSeqActionCnt )
    return array_rand ( $padSeqResult, count($padSeqResult) );
  else
    return array_rand ( $padSeqResult, $padSeqActionCnt );
  
?>