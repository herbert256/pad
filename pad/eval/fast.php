<?php

  global $padInfo;

  $kind  = 'pad';
  $name  = $eval;
  $count = 0;
  $parm  = [];

  if ( $padInfo )
    include PAD . 'events/functionsFast.php';

  return include PAD . "functions/$eval.php";

?>