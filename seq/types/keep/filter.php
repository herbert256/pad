<?php

  foreach ( $padSeqFilter as $padSeqFilterName => $padSeqFilterValue ) {

    $padSeqFilterParm = $padSeqParm;
    $padSeqFilterLoop = $padSeqLoop;

    padSeqSet ( $padSeqFilterName, $padSeqFilterValue );

    $padSeqFilterCheck = ( 'padSeqBool' . ucfirst($padSeqFilterName) ) ($padSeqLoop);
    
    $padSeqParm = $padSeqFilterParm;
    $padSeqLoop = $padSeqFilterLoop;

    if ( $padSeqFilterCheck === TRUE  and $padSeqSeq == 'remove' ) return FALSE;
    if ( $padSeqFilterCheck === FALSE and $padSeqSeq == 'keep'   ) return FALSE;

  }

  return TRUE;
 
?>