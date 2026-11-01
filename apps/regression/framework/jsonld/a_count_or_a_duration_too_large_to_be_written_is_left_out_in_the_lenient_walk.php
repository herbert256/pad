<?php

  // A count past what a number holds - INF, which json_encode refuses - and a duration in
  // words of more seconds than a whole number holds: errors under the strict check, left out
  // without it - the rest of the object written, never an empty block or a PHP error.

  $padCheckSyntax = FALSE;

  $huge = '1' . str_repeat ( '0', 330 );

?>
