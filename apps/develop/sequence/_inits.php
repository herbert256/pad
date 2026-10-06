<?php

  // Every page in this directory rewrites generated pages of the sequence application or the
  // engine's flags/ markers, and each is reachable as ?sequence/<name>: a plain GET of
  // ?sequence/build emptied pad/sequence/types/add/flags/ - files git tracks - before it
  // failed. Like develop's other action pages they act only when asked with go=.

  if ( ! isset ( $go ) )
    padRedirect ( 'index' );

?>
