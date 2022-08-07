<?php

  $pSeq_seq  = 'loop';
  $pSeq_set  = 'sequence';
  $pSeq_parm = TRUE;

  $pSeq_tmp = $pParm[$p];
  if ( $pTag[$p]== 'sequence' and pValid($pSeq_tmp) and isset($pSequenceStore [$pSeq_tmp]) )
    return include 'sequence/store.php';
  
  $pSeq_tmp = $pTag[$p];
  if ( pValid($pSeq_tmp) and isset($pSequenceStore [$pSeq_tmp]) )
    return include 'sequence/store.php';

  $pSeq_tmp = $pTag[$p];
  if ( file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pPrmsTag[$p]['type'] ?? '';
  if ( pValid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = array_key_first($pPrmsTag[$p]) ?? '';
  if ( pValid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pParm[$p] ?? '';
  if ( pValid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pTag[$p]?? '';
  if ( file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = $pPrmsTag[$p]['type'] ?? '';
  if ( pValid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = $pParm[$p] ?? '';
  if ( pValid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
   return include 'sequence/action.php';

  $pSeq_tmp = array_key_first($pPrmsTag[$p]) ?? '';
  if ( $pParm[$p] == '' and $pTag[$p]== 'sequence' and pValid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = array_key_first($pPrmsTag[$p]) ?? '';
  if ( pValid($pSeq_tmp) and isset($pSequenceStore [$pSeq_tmp]) )
    return include 'sequence/store.php';
 
  if ( strpos($pParm[$p], '..') ) {
    $pSeq_parm = $pParm[$p];
    return include 'sequence/range.php';
  } 

  if ( ctype_digit($pParm[$p]) ) {
    $pSeq_parm = "1..$pParm[$p]";
    return include 'sequence/range.php';
  } 

?>