<?php

  foreach ( $padSeqFilter as $padSeqFilterName => $padSeqFilterValue ) {

    $padSeqFilterParm = $padSeqParm;
    $padSeqFilterLoop = $padSeqLoop;

    padSeqSet ( $padSeqFilterName, $padSeqFilterValue );

    $padSeqFilterCheck = ( 'padSeqBool' . ucfirst( $padSeqFilterName) ) ($padSeqLoop) ;

    $padSeqParm = $padSeqFilterParm;
    $padSeqLoop = $padSeqFilterLoop;

    if ( $padSeqSeq == 'keep'   and $padSeqFilterCheck === FALSE ) return FALSE;
    if ( $padSeqSeq == 'remove' and $padSeqFilterCheck === TRUE  ) return FALSE;

  }

  return TRUE;

?>