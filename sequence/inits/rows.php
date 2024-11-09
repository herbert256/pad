<?php

  if ( $padSeqEntryParm === TRUE )
    $padSeqEntryParm = '';
  
  if ( $padSeqRows ) 
    return;

  if ( ! $padSeqParmUsed and ctype_digit ( $padOpt [$pad] [1] ) ) {
    $padSeqRows     = $padOpt [$pad] [1] ;
    $padSeqParmUsed = TRUE;
    return;
  }
  
  if     ( $padSeqTo <> PHP_INT_MAX                  ) $padSeqRows = PHP_INT_MAX ;
  elseif ( isset ( $padSeqEntryList [$pad] ['try'] ) ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqPull                               ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqStop <> PHP_INT_MAX                ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqSeq == 'pull'                      ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqFixed !== FALSE                    ) $padSeqRows = PHP_INT_MAX ;
  elseif ( $padSeqBuild == 'fixed'                   ) $padSeqRows = PHP_INT_MAX ;
  else                                                 $padSeqRows = 25;

?>