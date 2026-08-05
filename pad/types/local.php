<?php

  // Type handler for a local data file ({local:menu.json}, or a bare tag matching a file in a
  // _data/ directory): resolves the name to a path with padDataFileName() - the app directory
  // chain first, then _common, trying the known data extensions - and loads it through
  // types/_go/local.php.

  $padLocalFile = padDataFileName ( $padTag [$pad] ) ;

  return include PAD . 'types/_go/local.php';

?>