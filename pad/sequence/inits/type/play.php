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
    $padSeqParm   = $padSeqParm1;

  } else {

    padSeqCorrectParm2 ();

    if ( in_array ( $padSeqTag, ['make','keep','remove'] ) )
      $padSeqFirst  = $padSeqPrefix;
    else 
      $padSeqFirst  = $padSeqTag; 

    $padSeqSecond = $padSeqParm1; 
    $padSeqParm   = $padSeqParm2;

  }

  include 'sequence/inits/go/play.php';

?>