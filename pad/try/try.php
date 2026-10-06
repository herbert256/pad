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

  // Guards nest - a tag's handler (level/go) runs the tag's PHP through call/_try - and in
  // a page run they share one scope, so the inner guard set the one $padTry to its own
  // name: a throw after the inner include had returned went to the inner guard's catch
  // file. Each guard keeps its name on a stack, with the output buffers it began with -
  // the failed include's own, left open by the throw, go with it.

  $padTryStack [] = [ $padTry, ob_get_level () ];

  try {

    $padTryReturn = include PAD . $padTry;

  } catch (Throwable $padTryException) {

    [ $padTry, $padTryLevel ] = array_pop ( $padTryStack );

    while ( ob_get_level () > $padTryLevel )
      ob_end_clean ();

    return include PAD . "try/catch/$padTry";

  }

  $padTry = array_pop ( $padTryStack ) [0];

  return $padTryReturn;

?>
