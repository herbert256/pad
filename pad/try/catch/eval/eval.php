<?php

  // Catch handler for eval/eval.php, the expression evaluator behind padEval(): reports the
  // throwable through padErrorGo() and returns '' as the expression's value, so a bad pipe
  // or comparison does not abort the page.

  padErrorGo (
    'CATCH: ' .
    $padTryException->getMessage(),
    $padTryException->getFile(),
    $padTryException->getLine()
  );

  return '';

?>