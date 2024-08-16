<?php

  $padSeqLoop = $padSeqOprSeq;

  $padSeqOprCall = $padSeqOprType [$padSeqOprName];

   if ( $padSeqOprCall == 'function' )
     $padSeqOprSeq = ( 'padSeq' . ucfirst($padSeqOprName) ) ($padSeqLoop);
   else
    $padSeqOprSeq = include "$padSeqTypes/$padSeqOprName/$padSeqOprCall.php"; 
 
  if ( padSpecialValue ( $padSeqOprSeq ) ) 
    $padSeqOprSeq = $padSeqOprLst;  
  else                                        
    $padSeqOprLst = $padSeqOprSeq; 

  return $padSeqOprSeq;

?>