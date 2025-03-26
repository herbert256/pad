<?php

  // {make 'mySequence', 'add', 3 }
  // {make 'add', 'mysequence', 3 }

  // {make 'mySequence', 'add|3'  }
  // {make 'add|3', 'mysequence'  }

  $padSeqPlay  = $padSeqTag;
  $padSeqParm  = $padSeqPrm3;

  $padSeqFirst  = $padSeqPrm1; 
  $padSeqSecond = $padSeqPrm2; 

  include 'sequence/inits/go/play.php';

?>