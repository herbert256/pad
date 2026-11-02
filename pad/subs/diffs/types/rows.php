<?php

  // Records matched by a unique key, with added, removed and changed fields.
  return [
    'compare' => fn ( $a, $b, $options ) => padDiffRows ( $a, $b, $options ),
    'render' => fn ( $a, $b, $options ) => padDiffStructuredHtml ( padDiffRowChanges ( padDiffValue ( $a ), padDiffValue ( $b ), $options ['key'] ?? NULL, (bool) ( $options ['same'] ?? FALSE ) ), $options ['from'], $options ['to'], 'rows' ),
  ];

?>
