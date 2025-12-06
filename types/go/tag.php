<?php

  $padTagGo .= $padTag [$pad];

  $padCall = "$padTagGo.php";
  include PAD . 'call/ob.php';

  $padTagContent = $padCallOB . padFileGet ("$padTagGo.pad");

  return $padCallPHP;

?>