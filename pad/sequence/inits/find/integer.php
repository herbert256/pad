<?php

  if ( ! isset ( $padParms [$pad] [0] ['padPrmKind'] ) ) return;
  if ( $padParms [$pad] [0] ['padPrmKind'] <> 'parm'   ) return;
  if ( ! is_numeric ( $padSeqFindParm )                ) return;

  if     ( $padSeqSeq    and file_exists("sequence/types/$padSeqSeq/flags/parm")  ) $padSeqInt = 'sequence';
  elseif ( $padSeqAction and file_exists("sequence/actions/double/$padSeqAction") ) $padSeqInt = 'action';
  elseif ( $padSeqAction and file_exists("sequence/actions/parm/$padSeqAction")   ) $padSeqInt = 'action';
  else                                                                              $padSeqInt = '';

  if ( $padSeqInt == 'sequence' ) {

    $padSeqParm     = $padSeqFindParm;
    $padSeqFindParm = '';

  } elseif ( $padSeqInt == 'action' ) {

    $padSeqActionParm = $padSeqFindParm;
    $padSeqFindParm   = '';
  
  }  elseif ( ! $padSeqSeq ) {

    $padSeqSeq      = 'loop';
    $padSeqRows     = $padSeqFindParm;
    $padSeqFindParm = '';

  } 

?>