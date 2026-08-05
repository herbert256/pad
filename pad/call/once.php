<?php

  // include_once flavour of call/noOne.php: runs $padCall at most once per request and
  // returns its echoed output plus return value as content. Used by build/lib.php so a _lib
  // file shared by several directory levels defines its functions only once.

  include PAD . 'call/_once.php';

  return include PAD . 'call/_return.php';

?>