<?php

  // {make 'mySequence', 'add', 3 }
  // {make 'add', 'mysequence', 3 }

  // {make 'mySequence', 'add|3'  }
  // {make 'add|3', 'mysequence'  }

  padSeqCorrectParm3 ();

  $padSeqPlay  = $padSeqTag;
  $padSeqParm  = $padPrm3;

  $padSeqFirst  = $padPrm1; 
  $padSeqSecond = $padPrm2; 

  include 'sequence/inits/go/play.php';

?>