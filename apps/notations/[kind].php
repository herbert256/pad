<?php

  // A notation of the engine that has no page of its own here yet - ?name, /notations/name - with
  // what its file says of it and the examples of _examples/<kind>/ when there are any. Any
  // other name is a page not found.

  if ( ! is_string ( $kind ) or ! isset ( notationsCatalog () [$kind] ) )
    padAbort ( 404 );

  $title            = notationsCatalog () [$kind] ['name'];
  $notationKind     = $kind;
  $notationText     = notationsCatalog () [$kind] ['text'];
  $notationTag      = notationsCatalog () [$kind] ['tag'];
  $notationExamples = notationsExamples ( $kind );

?>
