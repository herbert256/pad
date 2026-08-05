<?php

  // Loaded by inits/config.php only when $padErrorTry is on, i.e. when the engine wraps its
  // risky include points in try/catch (see try/try.php and the handlers in try/catch/).
  //
  // One variable per protected include point - call/_try, call/_tryOnce, eval/eval,
  // level/go, level/var. They are placeholders ('xxx'): nothing in the engine reads them
  // yet, they just document which points are catchable and give applications a hook to
  // override.

  $padTryCallTry     = 'xxx';
  $padTryCallTryOnce = 'xxx';
  $padTryEvalEval    = 'xxx';
  $padTryLevelGo     = 'xxx';
  $padTryLevelVar    = 'xxx';

?>