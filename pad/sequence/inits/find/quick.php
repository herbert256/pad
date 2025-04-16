<?php

  if ( ! isset ( $padParms [$pad] [0] ['padPrmKind'] ) ) return;
  if ( $padParms [$pad] [0] ['padPrmKind'] <> 'parm'   ) return;

  if ( ! $padSeqSeq and strpos( $padSeqFindParm, '..' ) ) {
    $padSeqSeq      = 'range';
    $padSeqParm     = $padSeqFindParm;
    $padSeqFindParm = '';
  }

  if ( ! $padSeqSeq and strpos( $padSeqFindParm, ';' ) ) {
    $padSeqSeq      = 'list';
    $padSeqParm     = $padSeqFindParm;
    $padSeqFindParm = '';
  }

?>