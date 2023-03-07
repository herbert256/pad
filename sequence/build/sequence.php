<?php

  $padSeqSeq  = 'loop';
  $padSeqSet  = 'sequence';
  $padSeqParm = TRUE;

  $padSeqTmp = $padOpt [$pad] [1];
  if ( $padTag [$pad] == 'sequence' and padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';
  
  $padSeqTmp = $padTag [$pad];
  if ( padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';

  $padSeqTmp = $padTag [$pad];
  if ( padExists ( PAD . "sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padPrm [$pad]['type'] ?? '';
  if ( padValid($padSeqTmp) and padExists ( PAD . "sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padOpt [$pad] [1];
  if ( padValid($padSeqTmp) and padExists ( PAD . "sequence/types/$padSeqTmp" ) )  
    return include 'sequence/type.php';

  $padSeqTmp = $padTag [$pad] ?? '';
  if ( padExists ( PAD . "sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = $padPrm [$pad]['type'] ?? '';
  if ( padValid($padSeqTmp) and padExists ( PAD . "sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = $padOpt [$pad] [1] ;
  if ( padValid($padSeqTmp) and padExists ( PAD . "sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = $padOpt [$pad] [1] ;
  if ( $padOpt [$pad] [1] == '' and $padTag [$pad] == 'sequence' and padValid($padSeqTmp) and padExists ( PAD . "sequence/actions/$padSeqTmp.php" ) )
    return include 'sequence/action.php';

  $padSeqTmp = $padOpt [$pad] [1] ;
  if ( padValid($padSeqTmp) and isset($padSeqStore [$padSeqTmp]) )
    return include 'sequence/store.php';
 
  if ( strpos($padOpt [$pad] [1], '..') ) {
    $padSeqParm = $padOpt [$pad] [1];
    return include 'sequence/range.php';
  } 
 
  $padSeqTmp = array_key_first($padPrm [$pad]) ?? '';
  if ( padValid($padSeqTmp) and padExists ( PAD . "sequence/types/$padSeqTmp" ) )  {
    $padSeqParm = $padOpt [$pad] [1];
    return include 'sequence/type.php';
  }

  $padSeqTmp = array_key_first($padPrm [$pad]) ?? '';
  if ( padValid($padSeqTmp) and padExists ( PAD . "sequence/actions/$padSeqTmp.php" ) ) {
     $padSeqParm = $padOpt [$pad] [1];
    return include 'sequence/action.php';
 }

  if ( ctype_digit($padOpt [$pad] [1]) ) {
    $padSeqParm = "1.." . $padOpt [$pad] [1];
    return include 'sequence/range.php';
  } 

?>