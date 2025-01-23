<?php

  $padTagGo .= $padTag [$pad];

  $padCall = "$padTagGo.php";
  include 'call/ob.php';

  $padTagContent = $padCallOB . padFileGetContents ("$padTagGo.pad");

  return $padCallPHP;

?>