<?php

  $padTagGo .= $padTag [$pad];

  $padCall = "$padTagGo.php";
  include 'call/callNoOne.php';

  $padTagContent = $padCallOB . padFileGetContents ("$padTagGo.pad");

  return $padCallPHP;

?>