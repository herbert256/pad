<?php

  $padSeq_seq  = 'loop';
  $padSeq_set  = 'sequence';
  $padSeq_parm = TRUE;

  $padSeq_tmp = $padPrm [$pad];
  if ( $padTag [$pad] == 'sequence' and padValid($padSeq_tmp) and isset($padSequenceStore [$padSeq_tmp]) )
    return include 'sequence/store.php';
  
  $padSeq_tmp = $padTag [$pad];
  if ( padValid($padSeq_tmp) and isset($padSequenceStore [$padSeq_tmp]) )
    return include 'sequence/store.php';

  $padSeq_tmp = $padTag [$pad];
  if ( file_exists ( PAD . "sequence/types/$padSeq_tmp" ) )  
    return include 'sequence/type.php';

  $padSeq_tmp = $padPrmsTag [$pad]['type'] ?? '';
  if ( padValid($padSeq_tmp) and file_exists ( PAD . "sequence/types/$padSeq_tmp" ) )  
    return include 'sequence/type.php';

  $padSeq_tmp = array_key_first($padPrmsTag [$pad]) ?? '';
  if ( padValid($padSeq_tmp) and file_exists ( PAD . "sequence/types/$padSeq_tmp" ) )  
    return include 'sequence/type.php';

  $padSeq_tmp = $padPrm [$pad] ?? '';
  if ( padValid($padSeq_tmp) and file_exists ( PAD . "sequence/types/$padSeq_tmp" ) )  
    return include 'sequence/type.php';

  $padSeq_tmp = $padTag [$pad]?? '';
  if ( file_exists ( PAD . "sequence/actions/$padSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $padSeq_tmp = $padPrmsTag [$pad]['type'] ?? '';
  if ( padValid($padSeq_tmp) and file_exists ( PAD . "sequence/actions/$padSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $padSeq_tmp = $padPrm [$pad] ?? '';
  if ( padValid($padSeq_tmp) and file_exists ( PAD . "sequence/actions/$padSeq_tmp.php" ) )
   return include 'sequence/action.php';

  $padSeq_tmp = array_key_first($padPrmsTag [$pad]) ?? '';
  if ( $padPrm [$pad] == '' and $padTag [$pad] == 'sequence' and padValid($padSeq_tmp) and file_exists ( PAD . "sequence/actions/$padSeq_tmp.php" ) )
    return include 'sequence/action.php';

  $padSeq_tmp = array_key_first($padPrmsTag [$pad]) ?? '';
  if ( padValid($padSeq_tmp) and isset($padSequenceStore [$padSeq_tmp]) )
    return include 'sequence/store.php';
 
  if ( strpos($padPrm [$pad], '..') ) {
    $padSeq_parm = $padPrm [$pad];
    return include 'sequence/range.php';
  } 

  if ( ctype_digit($padPrm [$pad]) ) {
    $padSeq_parm = "1..$padPrm[$pad]";
    return include 'sequence/range.php';
  } 

?>