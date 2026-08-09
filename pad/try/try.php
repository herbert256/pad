<?php

  // Guarded include: runs PAD . $padTry and, when $padErrorTry is on, catches any
  // Throwable it raises and includes the matching handler under try/catch/ instead.
  //
  // The value of the include is returned either way, so a caller uses this exactly like a
  // plain include. The caller sets $padTry first; the pairs in use are level/go (a tag
  // handler), level/var (a variable tag), eval/eval (an expression), and call/_try and
  // call/_tryOnce (an app PHP file). With $padErrorTry off the call is made unguarded and
  // PHP's own error handling takes over.

  global $padErrorTry;

  if ( ! $padErrorTry )
    return include PAD . $padTry;

  try {

    return include PAD . $padTry;

  } catch (Throwable $padTryException) {

    return include PAD . "try/catch/$padTry";

  }

?>