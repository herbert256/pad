<?php

  if ( ! $padSeqFindParm )
    return;

      if (   $padSeqSeq    and file_exists("sequence/types/$padSeqSeq/flags/parm")  ) $padSeqParm       = $padSeqFindParm;
  elseif (   $padSeqAction and file_exists("sequence/actions/double/$padSeqAction") ) $padSeqActionParm = $padSeqFindParm;
  elseif (   $padSeqAction and file_exists("sequence/actions/parm/$padSeqAction")   ) $padSeqActionParm = $padSeqFindParm;

      if (   $padSeqSeq    and file_exists("sequence/types/$padSeqSeq/flags/parm")  ) return;
  elseif (   $padSeqAction and file_exists("sequence/actions/double/$padSeqAction") ) return;
  elseif (   $padSeqAction and file_exists("sequence/actions/parm/$padSeqAction")   ) return;

  if ( $padSeqSeq ) {

    $padSeqParm = $padSeqFindParm;

  } elseif ( is_numeric ( $padSeqFindParm ) ) {

    $padSeqSeq  = 'loop';
    $padSeqRows = $padSeqFindParm;

  } elseif ( strpos( $padSeqFindParm, '..' ) ) {
 
    $padSeqSeq  = 'range';
    $padSeqParm = $padSeqFindParm;

  }

  elseif ( strpos( $padSeqFindParm, ';' ) ) {
 
    $padSeqSeq  = 'list';
    $padSeqParm = $padSeqFindParm;

  } elseif ( $padSeqAction ) {

    $padSeqActionParm = $padSeqFindParm;
    $padSeqFindParm   = '';

  }
 
?>