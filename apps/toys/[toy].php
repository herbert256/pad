<?php

  // A toy, by the name in the address - ?life, /toys/life: one of the engine's toys, else a
  // page not found. Its template is shown beside it, coloured, and run.

  $one = array_values ( array_filter ( $toys, fn ( $item ) => $item ['toy'] === $toy ) ) [0] ?? NULL;

  if ( ! $one )
    padAbort ( 404 );

  $title    = $one ['name'];
  $text     = $one ['text'];
  $template = toysTemplate ( $toy );
  $source   = padHighlightTokens ( $template, 'pad' );

?>
