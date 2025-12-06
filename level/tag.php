<?php

  $padPairSet    = FALSE;
  $padBaseSet    = '';
  $padPrmTypeSet = ( count($padWords) > 1 ) ? 'open' : 'none';

  if ( substr($padBetween, -1) == '/') {
    $padBetween = substr($padBetween, 0, -1);
    include PAD . 'level/between.php';
    return;
  }

  if ( $padTypeSeq )
    $padPairTag = $padTypeSeq . ':' . $padTypeCheck;
  else
    $padPairTag = ( $padTypeGiven ) ? $padTypeResult . ':' . $padTypeCheck : $padTypeCheck;

  $padPos       = $padEnd [$pad];
  $padPairCheck = '';

  while ( ! in_array ( $padPairCheck, [' ', '}'] ) ) {

    $padPos = strpos($padOut [$pad] , '{/' . $padPairTag, ++$padPos);

    if ($padPos === FALSE)
      return;

    $padBaseBase = substr($padOut [$pad], $padEnd [$pad]+1, $padPos - $padEnd [$pad] - 1);

    if ( padOpenCloseCountOne ( $padBaseBase, $padPairTag) )
      $padPairCheck = substr($padOut [$pad], $padPos + strlen($padPairTag) + 2, 1);
    
  }

  include PAD . 'level/pair.php';
  
?>