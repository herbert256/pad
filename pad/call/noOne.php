<?php

  // Calls $padCall and returns its echoed output plus its return value as content, dropping
  // the bare 1 PHP hands back for a file with no return statement - hence the name. Used by
  // build/page.php for the _inits / page / _exits PHP files.

  include PAD . 'call/_call.php';

  if ($padCallPHP === 1)
    $padCallPHP = '';

  return include PAD . 'call/_return.php';

?>