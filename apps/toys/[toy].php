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

  // The icon at the top right of the template that takes it to the playground
  // (_common/_lib/playground.php).

  include_once COMMON . '_lib/playground.php';

  $play     = padPlayLink ( "$template\n" );

?>
