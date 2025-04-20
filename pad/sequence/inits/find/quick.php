<?php

  if ( $pqSeq or ! $pqFindParm ) 
    return;

  if ( strpos( $pqFindParm, '..' ) ) {
    $pqSeq      = 'range';
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

  if ( strpos( $pqFindParm, ';' ) ) {
    $pqSeq      = 'list';
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

  if ( ! isset ( $padParms [$pad] [0] ['padPrmKind'] ) ) return;
  if ( $padParms [$pad] [0] ['padPrmKind'] <> 'parm'   ) return;
  if ( ! is_numeric ( $pqFindParm )                    ) return;

  if     ( $pqSeq    and file_exists("sequence/types/$pqSeq/flags/parm")  ) return;
  elseif ( $pqAction and file_exists("sequence/actions/double/$pqAction") ) return;
  elseif ( $pqAction and file_exists("sequence/actions/parm/$pqAction")   ) return;

  $pqSeq      = 'loop';
  $pqRows     = $pqFindParm;
  $pqFindParm = '';

?>