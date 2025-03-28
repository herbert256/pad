<?php

  // {add:make sequence, 5}
  // {sequence:make add, 5}

  // {make:sequence add, 5}
  // {make:add sequence, 5}

  // {add:mySequence 5}
  // {mySequence:add 5}

  // {add:mySequence make, 3 } {$mySequence} {/mySequence:mySequence}

  if ( $padSeqPrefix and file_exists ( "sequence/types/$padSeqPrefix" ) )
    if ( isset ( $padSeqStore [$padSeqTag] ) )
      if ( in_array ( $padSeqFirst, ['make','keep','remove'] ) ) {
        $padSeqPull    = $padSeqTag;
        $padSeqSeq     = $padSeqPrefix;
        $padSeqType    = $padSeqFirst;
        $padSeqDone [] = $padSeqFirst;
        $padSeqPlay    = $padSeqFirst;
        return;
      }
      
  $padSeqTmp1 = '';
  $padSeqTmp2 = '';

  if ( $padSeqType == 'make' and $padSeqTag <> 'make' and $padSeqPrefix <> 'make' ) {

    $padSeqTmp1 = $padSeqPrefix;
    $padSeqTmp2 = $padSeqTag; 

  } else {

    if ( in_array ( $padSeqTag, ['make','keep','remove'] ) )
      $padSeqTmp1  = $padSeqPrefix;
    else 
      $padSeqTmp1  = $padSeqTag; 

    if ( $padSeqFirst and file_exists ( "sequence/types/$padSeqFirst" ) ) {
      
      $padSeqTmp2    = $padSeqFirst; 
      $padSeqDone [] = $padSeqFirst;

       if ( $padSeqFirstParm and $padSeqFirstParm !== TRUE and ! $padSeqParm )
        $padSeqParm = $padSeqFirstParm;
      
    } 

  }

  if ( $padSeqTmp1 and $padSeqTmp2 ) {

    $padSeqPlay = $padSeqType;

    if ( isset ( $padSeqStore[$padSeqTmp1] ) and file_exists ( "sequence/types/$padSeqTmp2" ) ) {
      $padSeqPull = $padSeqTmp1;
      $padSeqSeq  = $padSeqTmp2;
    } elseif ( isset ( $padSeqStore[$padSeqTmp2] ) and file_exists ( "sequence/types/$padSeqTmp1" ) ) {
      $padSeqPull = $padSeqTmp2;
      $padSeqSeq  = $padSeqTmp1;
    } 

  }

?>