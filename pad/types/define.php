<?php

  // Type handler for a {define} of the template: its body is the tag's template, as the
  // .pad of an application tag is - the caller's content goes to its @content@ and its
  // slots, its parameters are read as {#name}. lib/macro.php.

  $padTagContent = $padDefineStore [$padTag [$pad]];

  return TRUE;

?>
