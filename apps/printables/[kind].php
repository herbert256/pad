<?php

  // A printable, by the name in the address - ?labels, /printables/labels: one of the engine's
  // kinds, else a page not found. Its template - kinds/<kind>.pad, sample data from {fake:rows} with
  // fixed seeds - is shown beside what it makes, coloured, and run; its pdf option links the
  // same sheets as a PDF.

  $one = array_values ( array_filter ( $printables, fn ( $item ) => $item ['kind'] === $kind ) ) [0] ?? NULL;

  if ( ! $one )
    padAbort ( 404 );

  $title    = $one ['name'];
  $text     = $one ['text'];
  $template = printablesTemplate ( $kind );
  $source   = padHighlightTokens ( $template, 'pad' );

  // The icons at the top right of the template that take it to the WASM editor and the live
  // application (_common/_lib/playground.php).

  include_once COMMON . '_lib/playground.php';

  $play     = padPlayLink ( "$template\n" );

?>
