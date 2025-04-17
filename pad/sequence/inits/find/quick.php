<?php

  if ( $padSeqSeq or ! $padSeqFindParm ) 
    return;

  if ( strpos( $padSeqFindParm, '..' ) ) {
    $padSeqSeq      = 'range';
    $padSeqParm     = $padSeqFindParm;
    $padSeqFindParm = '';
  }

  if ( strpos( $padSeqFindParm, ';' ) ) {
    $padSeqSeq      = 'list';
    $padSeqParm     = $padSeqFindParm;
    $padSeqFindParm = '';
  }

  if ( ! isset ( $padParms [$pad] [0] ['padPrmKind'] ) ) return;
  if ( $padParms [$pad] [0] ['padPrmKind'] <> 'parm'   ) return;
  if ( ! is_numeric ( $padSeqFindParm )                ) return;

  if     ( $padSeqSeq    and file_exists("sequence/types/$padSeqSeq/flags/parm")  ) return;
  elseif ( $padSeqAction and file_exists("sequence/actions/double/$padSeqAction") ) return;
  elseif ( $padSeqAction and file_exists("sequence/actions/parm/$padSeqAction")   ) return;

  $padSeqSeq      = 'loop';
  $padSeqRows     = $padSeqFindParm;
  $padSeqFindParm = '';

?>