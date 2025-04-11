<?php

  if ( $padSeqRows ) 
    return;
  
  if     ( $padSeqTo <> PHP_INT_MAX                  ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqTry <> 10000                       ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqStop <> PHP_INT_MAX                ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqFixed !== FALSE                    ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqBuild == 'fixed'                   ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqBuild == 'start'                   ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqBuild == 'pull'                   ) $padSeqRows = PHP_INT_MAX ;
  else                                                 $padSeqRows = 25;

?>