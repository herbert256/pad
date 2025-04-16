<?php

  if ( ! $padSeqSeq and $padSeqFindParm and $padSeqFindType == 'sequence' and is_numeric ( $padSeqFindParm ) ) {
    $padSeqSeq      = 'loop';
    $padSeqRows     = $padSeqFindParm;
  }

      if ( strpos     ( $padSeqFindParm, '..' ) ) $padSeqSeq = 'range';
  elseif ( strpos     ( $padSeqFindParm, ';'  ) ) $padSeqSeq = 'list';

  if ( $padSeqSeq ) {
    $padSeqParm     = $padSeqFindParm;
    $padSeqFindParm = '';
  }
  
?>