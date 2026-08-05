<?php

  // Fast path for an expression that is nothing but the name of a built-in pipe function.
  //
  // Included by eval/eval.php when PAD/functions/$eval.php exists: parsing is skipped and the
  // function file is included directly, after setting up the $kind/$name/$count/$parm contract
  // those files share with the slow path. $value is already in scope. Returns its result.

  global $padInfo;

  $kind  = 'pad';
  $name  = $eval;
  $count = 0;
  $parm  = [];

  if ( $padInfo )
    include PAD . 'events/functionsFast.php';

  return include PAD . "functions/$eval.php";

?>