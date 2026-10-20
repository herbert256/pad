<?php

  // With the try guard off - as it is while an _events/error.php hook runs - a throw goes
  // out of padEval, and a caller may catch it and go on.

  function evalOpenThrows () { throw new RuntimeException ( 'thrown' ); }

  $evalOpenTry = $padErrorTry;
  $padErrorTry = FALSE;

  $evalOpenBefore = (int) ( $padEvalBusy ?? 0 );
  padRescue ( fn () => padEval ( 'php:evalOpenThrows()' ), '', FALSE );
  $evalOpenAfter  = (int) ( $padEvalBusy ?? 0 );

  $padErrorTry = $evalOpenTry;

?>
