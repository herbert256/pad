<?php

  $padSeqSeq  = 'loop';
  $padSeqSet  = 'sequence';
  $padSeqParm = TRUE;

  $padSeqTmp = $padPrm [$pad];
  if ( $padTag [$pad] == 'sequence' and padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';
  
  $padSeqTmp = $padTag [$pad];
  if ( padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';

  $padSeqTmp = $padTag [$pad];
  if ( file_exists ( PAD . "sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padPrmsTag [$pad]['type'] ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = array_key_first($padPrmsTag [$pad]) ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padPrm [$pad] ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padTag [$pad]?? '';
  if ( file_exists ( PAD . "sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = $padPrmsTag [$pad]['type'] ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = $padPrm [$pad] ?? '';
  if ( padValid($padSeqTmp) and file_exists ( PAD . "sequence/actions/$padSeqTmp.php" ) )
   return include 'sequence/action.php';

  $padSeqTmp = array_key_first($padPrmsTag [$pad]) ?? '';
  if ( $padPrm [$pad] == '' and $padTag [$pad] == 'sequence' and padValid($padSeqTmp) and file_exists ( PAD . "sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = array_key_first($padPrmsTag [$pad]) ?? '';
  if ( padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';
 
  if ( strpos($padPrm [$pad], '..') ) {
    $padSeqParm = $padPrm [$pad];
    return include 'sequence/range.php';
  } 

  if ( ctype_digit($padPrm [$pad]) ) {
    $padSeqParm = "1..$padPrm[$pad]";
    return include 'sequence/range.php';
  } 

?>