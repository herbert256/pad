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

  // Through the same door as the slow path, so a function that needs parameters is held
  // to them here too - {$x | replace} read a missing $parm [0] before anything could speak.

  return include PAD . 'eval/parms/pad.php';

?>
