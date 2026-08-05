<?php

  // Catch handler for level/go.php, a tag handler that threw. With the notOk option the
  // level takes its documented not-ok path (options/notOk.php) and the error is swallowed;
  // otherwise the throwable is reported through padErrorGo() and the level carries on with
  // whatever $padTagResult it had.

  if  ( padTagParm ( 'notOk' ) )
    return include PAD . 'options/notOk.php';

  padErrorGo (
    'CATCH: ' .
    $padTryException->getMessage(),
    $padTryException->getFile(),
    $padTryException->getLine()
  );

?>