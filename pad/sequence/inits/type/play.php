<?php

  // {add:make, 'sequence', 5}
  // {sequence:make, 'add', 5}

  // {make:sequence, 'add', 5}
  // {make:add, 'sequence', 5}

  // {add:mySequence 3}
  // {mySequence:add 3}

  $padSeqPlay = $padSeqType;
  
  if ( $padSeqType == 'make' and $padSeqTag <> 'make' and $padSeqPrefix <> 'make' ) {

    $padSeqFirst  = $padSeqPrefix;
    $padSeqSecond = $padSeqTag; 
    $padSeqParm   = $padSeqPrm1;

  } else {

    if ( in_array ( $padSeqTag, ['make','keep','remove'] ) )
      $padSeqFirst  = $padSeqPrefix;
    else 
      $padSeqFirst  = $padSeqTag; 

    $padSeqSecond = $padSeqPrm1; 
    $padSeqParm   = $padSeqPrm2;

  }

  include 'sequence/inits/go/play.php';

?>