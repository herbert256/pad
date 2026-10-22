<?php

  // Implements null="name": hands 'null' to options/_go/reset.php, which puts the named content
  // in place of the level's own. Included by level/flags.php when the tag returned NULL or
  // NaN - INF is a value, which prints.

  $padReset = 'null';

  return include PAD . 'options/_go/reset.php';

?>
