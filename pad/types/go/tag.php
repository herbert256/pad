<?php

  $padTagGo .= $padTag [$pad];

  $padCall = "$padTagGo.php";
  include PAD . 'call/callNoOne.php';

  $padTagContent = $padCallOB . padFileGetContents ("$padTagGo.pad");

  return $padCallPHP;

?>