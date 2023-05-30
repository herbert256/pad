<?php

  $padTagGo .= "_tags/". $padTag [$pad];

  $padCall = "$padTagGo.php";
  include pad . 'call/callNoOne.php';

  $padTagContent = $padCallOB . padFileGetContents ("$padTagGo.html") ;

  padTrueFalse ( $padTagContent , $padTagTrue, $padTagFalse );

  if     ( $padCallPHP === TRUE       ) $padContent     = $padTagTrue  . $padContent;
  elseif ( $padCallPHP === FALSE      ) $padElse [$pad] = $padTagFalse . $padElse [$pad];
  elseif ( $padCallPHP === NULL       ) $padContent     = '';
  elseif ( ! is_array ( $padCallPHP ) ) $padContent     = $padCallPHP  . $padTagTrue . $padContent;
  elseif ( count ( $padCallPHP )      ) $padContent     = $padTagTrue  . $padContent;
  else                                  $padElse [$pad] = $padTagFalse . $padElse [$pad];

  return $padCallPHP;

?>