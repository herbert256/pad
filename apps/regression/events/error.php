<?php

  // Reads a variable that was never set: under this application's 'ignore' action the page
  // goes on, and the error hook has heard the warning by the time the next line runs.

  $value  = $neverSetAnywhere;
  $caught = $GLOBALS ['heardErrors'] [0] ?? 'nothing';

?>
