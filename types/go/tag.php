<?php

  $padTagGo .= "_tags/". $padTag [$pad];

  $padCall   = "$padTagGo.php";
  $padTagPhp = include pad . 'call/anyNoOne.php';

  if ( ! is_array($padTagPhp) and $padTagPhp !== TRUE and $padTagPhp !== FALSE and $padTagPhp !== NULL )
    $padTagAdd .= $padTagPhp;
  else
    $padTagAdd = '';
 
  $padTagContent = $padCallOB . $padTagAdd . padFileGetContents ("$padTagGo.html");

  padGetTrueFalse ( $padTagContent, $padTagTrue, $padTagFalse );

  if     ( $padTagPhp === TRUE       ) $padTagTrueFalse = TRUE;
  elseif ( $padTagPhp === FALSE      ) $padTagTrueFalse = FALSE;
  elseif ( ! is_array ( $padTagPhp ) ) $padTagTrueFalse = TRUE;
  elseif ( count ( $padTagPhp )      ) $padTagTrueFalse = TRUE;
  else                                 $padTagTrueFalse = FALSE;

  if ( $padTagTrueFalse === FALSE )
    $padFalse [$pad] .= $padTagFalse;

if ( is_array ( $padTagPhp ) ) {
  $padData [$pad] = $padTagPhp;
  
}

  if ( $padTagPhp === TRUE AND $padTagTrue <> '' )
    return $padTagTrue;

  return $padTagPhp;

?>