<?php

  // Implements else="name": hands 'else' to options/go/reset.php, which puts the named content
  // in place of the level's own and turns the miss back into a hit.
  //
  // Included by level/flags.php when the tag produced an empty array, FALSE or '', and once
  // early by level/start.php.

  $padReset = 'else';

  return include PAD . 'options/go/reset.php';

?>