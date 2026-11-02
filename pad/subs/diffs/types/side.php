<?php

  // Old and new lines side by side with changed words highlighted.
  return [
    'render' => fn ( $a, $b, $options ) => padDiffLines ( $a, $b, TRUE, $options ['from'], $options ['to'] ),
  ];

?>
