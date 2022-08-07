<?php

  $pSeq_seq  = 'loop';
  $pSeq_set  = 'sequence';
  $pSeq_parm = TRUE;

  $pSeq_tmp = $pParm[$p];
  if ( $pTag == 'sequence' and pad_valid($pSeq_tmp) and isset($pSequence_store [$pSeq_tmp]) )
    return include 'sequence/store.php';
  
  $pSeq_tmp = $pTag[$p];
  if ( pad_valid($pSeq_tmp) and isset($pSequence_store [$pSeq_tmp]) )
    return include 'sequence/store.php';

  $pSeq_tmp = $pTag[$p];
  if ( file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pPrmsTag[$p]['type'] ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = array_key_first($pPrmsTag[$p]) ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pParm[$p] ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pTag ?? '';
  if ( file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = $pPrmsTag[$p]['type'] ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = $pParm[$p] ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
   return include 'sequence/action.php';

  $pSeq_tmp = array_key_first($pPrmsTag[$p]) ?? '';
  if ( $pParm[$p] == '' and $pTag == 'sequence' and pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = array_key_first($pPrmsTag[$p]) ?? '';
  if ( pad_valid($pSeq_tmp) and isset($pSequence_store [$pSeq_tmp]) )
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