<?php

  $padPos        = $padEnd [$pad];
  $padPairCheck  = '';

  while ( ! in_array ( $padPairCheck, [' ', '}'] ) ) {

    $padPos = strpos($padHtml [$pad] , '{/' . $padPairTagClose, ++$padPos);

    if ($padPos === FALSE)
      return FALSE;

    $padTrueBase = substr($padHtml [$pad], $padEnd [$pad]+1, $padPos - $padEnd [$pad] - 1);

    if ( padOpenCloseCountOne ( $padTrueBase, $padPairTag) )
      $padPairCheck = substr($padHtml [$pad], $padPos + strlen($padPairTagClose) + 2, 1);
    
  }
 
  $padTrueSet    = substr ( $padTrueBase, 0, $padPos );
  $padEnd [$pad] = strpos ( $padHtml [$pad], '}', $padPos+2 );

  if ( $padEnd [$pad] === FALSE )
    return NULL;

  return TRUE;

?>