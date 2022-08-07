<?php

  $pSeq_seq  = 'loop';
  $pSeq_set  = 'sequence';
  $pSeq_parm = TRUE;

  $pSeq_tmp = $pParm;
  if ( $pTag == 'sequence' and pad_valid($pSeq_tmp) and isset($pSequence_store [$pSeq_tmp]) )
    return include 'sequence/store.php';
  
  $pSeq_tmp = $pTag;
  if ( pad_valid($pSeq_tmp) and isset($pSequence_store [$pSeq_tmp]) )
    return include 'sequence/store.php';

  $pSeq_tmp = $pTag;
  if ( file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pPrms_tag['type'] ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = array_key_first($pPrms_tag) ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pParm ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/types/$pSeq_tmp" ) )  
    return include 'sequence/type.php';

  $pSeq_tmp = $pTag ?? '';
  if ( file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = $pPrms_tag['type'] ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = $pParm ?? '';
  if ( pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
   return include 'sequence/action.php';

  $pSeq_tmp = array_key_first($pPrms_tag) ?? '';
  if ( $pParm == '' and $pTag == 'sequence' and pad_valid($pSeq_tmp) and file_exists ( PAD . "sequence/actions/$pSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $pSeq_tmp = array_key_first($pPrms_tag) ?? '';
  if ( pad_valid($pSeq_tmp) and isset($pSequence_store [$pSeq_tmp]) )
    return include 'sequence/store.php';
 
  if ( strpos($pParm, '..') ) {
    $pSeq_parm = $pParm;
    return include 'sequence/range.php';
  } 

  if ( ctype_digit($pParm) ) {
    $pSeq_parm = "1..$pParm";
    return include 'sequence/range.php';
  } 

?>