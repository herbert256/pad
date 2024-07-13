<?php

  $padTagGo .= $padTag [$pad];

  $padCall = "$padTagGo.php";
  include pad . 'call/callNoOne.php';

  $padTagContent = $padCallOB . padFileGetContents ("$padTagGo.pad");

  return $padCallPHP;

?>