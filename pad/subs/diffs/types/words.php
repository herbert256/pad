<?php

  // Changed words marked as escaped deletions and insertions.
  return [
    'compare' => fn ( $a, $b, $options ) => padDiffTextRecords ( $a, $b, FALSE ),
    'render' => fn ( $a, $b, $options ) => ( $options ['side'] ?? FALSE ) || ( $options ['lines'] ?? FALSE )
      ? padDiffLines ( $a, $b, (bool) ( $options ['side'] ?? FALSE ), $options ['from'], $options ['to'] )
      : padDiffWords ( $a, $b ),
  ];

?>
