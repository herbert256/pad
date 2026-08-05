<?php

  // content: - returns a named block from the content store, where the content option and the
  // {content} tags park rendered output for later reuse.

  global $padContentStore;

  return $padContentStore [$name];

?>