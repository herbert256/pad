<?php

  // {make 'mySequence', 'add', 3 }
  // {make 'add', 'mysequence', 3 }

  // {make 'mySequence', 'add|3'  }
  // {make 'add|3', 'mysequence'  }

  padSeqCorrectParm3 ();

  $padSeqPlay  = $padSeqTag;
  $padSeqParm  = $padSeqParm3;

  $padSeqFirst  = $padSeqParm1; 
  $padSeqSecond = $padSeqParm2; 

  include 'sequence/inits/go/play.php';

?>