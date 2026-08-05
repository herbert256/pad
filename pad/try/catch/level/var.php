<?php

  // Catch handler for level/var.php, a variable tag that threw. The throwable is reported
  // through padErrorGo() and padLevel('') replaces the {$...} in the output with nothing,
  // so the surrounding page still renders.

  padErrorGo (
    'CATCH: ' .
    $padTryException->getMessage(),
    $padTryException->getFile(),
    $padTryException->getLine()
  );

  padLevel ( '' );

?>