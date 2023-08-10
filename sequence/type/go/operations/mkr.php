<?php

  foreach ( $padSeqOprVal as $padSeqOprOne => $padSeqOprPrm ) {

    padSeqSet ( $padSeqOprOne, $padSeqOprPrm );

    $padSeqLoop = $padSeqOprSeq;

    if ( $padSeqOprName == 'make')
      $padSeqOprSeq = include "$padSeqTypes/$padSeqOprOne/make.php"; 
    else
      $padSeqOprSeq = include "$padSeqTypes/$padSeqOprOne/filter.php"; 

    if ( ( $padSeqOprSeq === TRUE  and $padSeqOprName == 'remove' ) or 
         ( $padSeqOprSeq === FALSE and $padSeqOprName == 'keep'   ) )   
      return TRUE;

    if ( padSpecialValue ( $padSeqOprSeq ) ) 
      $padSeqOprSeq = $padSeqOprLst;  
    else                                        
      $padSeqOprLst = $padSeqOprSeq; 

  }

  return $padSeqOprSeq;

?>