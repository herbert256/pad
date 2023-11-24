<?php

  foreach ( $padSeqOprVal as $padSeqOprOne => $padSeqOprPrm ) {

    padSeqSet ( $padSeqOprOne, $padSeqOprPrm );

    $padSeqLoop = $padSeqOprSeq;

    if ( $padSeqOprName == 'make') {
      
      if ( $padSeqOprType [$padSeqOprOne] == 'function' )
        $padSeqOprSeq  = ( 'padSeq' . ucfirst($padSeqOprOne) ) ($padSeqLoop);
      else
        $padSeqOprSeq = include "$padSeqTypes/$padSeqOprOne/$padSeqOprType[$padSeqOprOne].php"; 
 
    }
 
    else {
 
      $padSeqOprSeq = ( 'padSeqBool' . ucfirst($padSeqOprOne) ) ($padSeqLoop);

      if ( ( $padSeqOprSeq === TRUE  and $padSeqOprName == 'remove' ) or 
           ( $padSeqOprSeq === FALSE and $padSeqOprName == 'keep'   ) )   
        return TRUE;

    }

    if ( padSpecialValue ( $padSeqOprSeq ) ) 
      $padSeqOprSeq = $padSeqOprLst;  
    else                                        
      $padSeqOprLst = $padSeqOprSeq; 

  }

  return $padSeqOprSeq;

?>