<?php

  foreach ( $padSeqFilter as $padSeqFilterName => $padSeqFilterValue ) {

    $padSeqFilterParm = $padSeqParm;
    $padSeqFilterLoop = $padSeqLoop;
    $padSeqFilterOrg  = $padSeqLoop;

    padSeqSet ( $padSeqFilterName, $padSeqFilterValue );

    $padSeqTmp = $padSeqFilterType [$padSeqFilterName];

    if     ( $padSeqTmp == 'fixed'    ) $padSeqFilterCheck = $padSeqLoop;
    elseif ( $padSeqTmp == 'function' ) $padSeqFilterCheck = ( 'padSeq'     . ucfirst($padSeqFilterName) ) ($padSeqLoop);
    elseif ( $padSeqTmp == 'bool'     ) $padSeqFilterCheck = ( 'padSeqBool' . ucfirst($padSeqFilterName) ) ($padSeqLoop);
    else                                $padSeqFilterCheck = include "$padSeqTypes/$padSeqFilterName/$padSeqBuild.php";    

    $padSeqParm = $padSeqFilterParm;
    $padSeqLoop = $padSeqFilterLoop;

    if     ( $padSeqFilterCheck === NULL ) $padSeqFilterCheck = FALSE;
    elseif ( $padSeqFilterCheck === INF  ) $padSeqFilterCheck = FALSE; 
    elseif ( $padSeqFilterCheck === NAN  ) $padSeqFilterCheck = FALSE; 

    if ( $padSeqFilterCheck === TRUE            and $padSeqSeq == 'remove' ) return FALSE;
    if ( $padSeqFilterCheck === FALSE           and $padSeqSeq == 'keep'   ) return FALSE;
    if ( $padSeqFilterCheck == $padSeqFilterOrg and $padSeqSeq == 'remove' ) return FALSE;
    if ( $padSeqFilterCheck <> $padSeqFilterOrg and $padSeqSeq == 'keep'   ) return FALSE;

  }

  return TRUE;
 
?>