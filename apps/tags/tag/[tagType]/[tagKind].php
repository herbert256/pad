<?php

  // The page of a kind - a chart type, a game, a toy, an icon, a sequence type ... - by the
  // two names of its address, ?tag/chart/pie: the frame of every tag page around it
  // (tag/_inits.pad, tag/_exits.pad) and what tag/_exits.php reads of the kind. A name that is
  // no kind is a page not found.

  if ( ! is_string ( $tagType ) or ! is_string ( $tagKind ) or ! isset ( tagsCatalog () ["$tagType:$tagKind"] ) )
    padAbort ( 404 );

?>
