<?php

  // Every page in this directory rewrites generated pages of the sequence application or the
  // engine's flags/ markers, and each is reachable as ?sequence/<name>: a plain GET of
  // ?sequence/build emptied pad/sequence/types/add/flags/ - files git tracks - before it
  // failed. Like develop's other action pages they act only when asked with go=.
  //
  // And only the build runs: the others are its steps, and asked on their own they took
  // $type from the request - ?sequence/basic&go=1&type=../../x wrote a page of the asker's
  // text wherever the path pointed, ?sequence/flags emptied the flags/ directory it named.
  // Inside the build a type is one of the engine's own directory names.

  if ( ! isset ( $go ) or $padPage != 'sequence/build' )
    padRedirect ( 'index' );

?>
