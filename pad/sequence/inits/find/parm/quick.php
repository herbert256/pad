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

      if ( ! $pqFindParm                                                  ) return;
  elseif ( ! is_numeric ( $pqFindParm )                                   ) return;
  elseif ( ! isset ( $padParms [$pad] [0] ['padPrmKind'] )                ) return;
  elseif ( $padParms [$pad] [0] ['padPrmKind'] <> 'parm'                  ) return;
  elseif ( $pqSeq    and file_exists ( PT . "$pqSeq/flags/parm")  ) return;
  elseif ( $pqAction and file_exists ( PQ . "actions/double/$pqAction") ) return;
  elseif ( $pqAction and file_exists ( PQ . "actions/parm/$pqAction")   ) return;

  $pqSeq      = 'loop';
  $pqRows     = $pqFindParm;
  $pqFindParm = '';

?>