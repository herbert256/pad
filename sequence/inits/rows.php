<?php

  if     ( $padSeqRows                       ) return;
  elseif ( $padSeqTo <> PHP_INT_MAX          ) $padSeqRows = PHP_INT_MAX ;
  elseif ( isset ( $padPrm [$pad] ['try'] )  ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqPull                       ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqSeq == 'pull'              ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqFixed !== FALSE            ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqBuild == 'fixed'           ) $padSeqRows = PHP_INT_MAX ;
  else                                         $padSeqRows = 25;

?>