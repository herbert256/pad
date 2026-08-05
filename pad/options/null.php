<?php

  // Implements null="name": hands 'null' to options/go/reset.php, which puts the named content
  // in place of the level's own. Included by level/flags.php when the tag returned NULL, INF
  // or NaN.

  $padReset = 'null';

  return include PAD . 'options/go/reset.php';

?>