<?php

  // A line comparison with old and new line numbers and changed words.
  return [
    'compare' => fn ( $a, $b, $options ) => padDiffTextRecords ( $a, $b, TRUE ),
    'render' => fn ( $a, $b, $options ) => padDiffLines ( $a, $b, FALSE, $options ['from'], $options ['to'] ),
  ];

?>
