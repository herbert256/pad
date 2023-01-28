<?php

  $padSeqList      = $padSeqLoop;
  $padSeqListLast = $padSeqLoop;

  foreach ( $GLOBALS["padSeq_$padSeqOprName"."_list"] as $padSeqListName => $padSeqListValue ) {

    padSeqSet ( $padSeqListName, $padSeqListValue );

    $padSeqLoop = $padSeqList;

    if ( $padSeqOprName== 'make' )
      $padSeqList = include PAD . "pad/sequence/types/$padSeqListName/make.php"; 
    else
      $padSeqList = include PAD . "pad/sequence/types/$padSeqListName/filter.php"; 

    if     ( $padSeqOprName == 'keep'   and $padSeqList === FALSE ) return FALSE;
    elseif ( $padSeqOprName == 'remove' and $padSeqList === TRUE  ) return FALSE;
    elseif ( $padSeqOprName == 'keep'   and $padSeqList === TRUE  ) $padSeqList = $padSeqListLast;
    elseif ( $padSeqOprName == 'remove' and $padSeqList === FALSE ) $padSeqList = $padSeqListLast;
    elseif ( $padSeqList === NULL                                    ) return NULL;
    elseif ( $padSeqList === INF                                     ) return NULL; 
    elseif ( $padSeqList === NAN                                     ) return NULL; 
    elseif ( $padSeqList === TRUE                                    ) $padSeqList = $padSeqListLast;
    elseif ( $padSeqList === FALSE                                   ) return FALSE;

   $padSeqListLast = $padSeqList;

  }

  return $padSeqList;

?>