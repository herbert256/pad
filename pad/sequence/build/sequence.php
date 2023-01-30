<?php

  $padSeqSeq  = 'loop';
  $padSeqSet  = 'sequence';
  $padSeqParm = TRUE;

  $padSeqTmp = $padPrm [$pad] [1];
  if ( $padTag [$pad] == 'sequence' and padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';
  
  $padSeqTmp = $padTag [$pad];
  if ( padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';

  $padSeqTmp = $padTag [$pad];
  if ( file_exists ( PAD . "pad/sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padPrm [$pad]['type'] ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "pad/sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = array_key_first($padPrm [$pad]) ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "pad/sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padPrm [$pad] [1] ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "pad/sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padTag [$pad]?? '';
  if ( file_exists ( PAD . "pad/sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = $padPrm [$pad]['type'] ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "pad/sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = $padPrm [$pad] [1] ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "pad/sequence/actions/$padSeqTmp.php" ) )
   return include 'sequence/action.php';

  $padSeqTmp = array_key_first($padPrm [$pad]) ?? '';
  if ( $padPrm [$pad] [1] == '' and $padTag [$pad] == 'sequence' and padValid($padSeqTmp) and file_exists ( PAD . "pad/sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = array_key_first($padPrm [$pad]) ?? '';
  if ( padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';
 
  if ( strpos($padPrm [$pad] [1], '..') ) {
    $padSeqParm = $padPrm [$pad] [1];
    return include 'sequence/range.php';
  } 

  if ( ctype_digit($padPrm [$pad] [1]) ) {
    $padSeqParm = "1..$padPrm [$pad] [1]";
    return include 'sequence/range.php';
  } 

?>