<?php

  // Type handler for an application tag: finds _tags/<name> in the app's directory chain, the
  // page's own directory first so a subdirectory can override a parent's tag, and loads the
  // .php/.pad pair through types/_go/tag.php.

  $padTagGo = APP2 . padAppTagCheck ( $padTag [$pad] );

  return include PAD . 'types/_go/tag.php';

?>