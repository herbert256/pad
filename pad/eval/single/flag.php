<?php

  // flag: - returns a named boolean from the bool store that the toBool option and {bool ...}
  // fill in. Note this reads the bool store, not the sequence flags that the tag-level
  // {flag:...} type reaches through the sequence subsystem.

  global $padBoolStore;

  return $padBoolStore [$name];

?>