<?php

  // Structural JSON changes by escaped JSON Pointer path, preserving types.
  return [
    'compare' => fn ( $a, $b, $options ) => padDiffJson ( $a, $b ),
    'render' => fn ( $a, $b, $options ) => padDiffStructuredHtml ( padDiffJsonChanges ( padDiffValue ( $a ), padDiffValue ( $b ), (bool) ( $options ['same'] ?? FALSE ) ), $options ['from'], $options ['to'], 'json' ),
  ];

?>
