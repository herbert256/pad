<?php

  $padSeqLoop = $padSeqOprSeq;

  $padSeqOprSeq = include "$padSeqTypes/$padSeqOprName/make.php";

  if ( padSpecialValue ( $padSeqOprSeq ) ) 
    $padSeqOprSeq = $padSeqOprLst;  
  else                                        
    $padSeqOprLst = $padSeqOprSeq; 

  return $padSeqOprSeq;

?>