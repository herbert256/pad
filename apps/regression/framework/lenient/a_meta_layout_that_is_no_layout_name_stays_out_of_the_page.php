<?php

  // The lenient walk under test: with the syntax check off, what cannot run passes the
  // value through or yields empty, and the walk carries on.

  $padCheckSyntax = FALSE;

  $layout = 'nolayout}{echo 6 * 7}{x';

?>
