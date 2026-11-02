<?php

  // diagram:kind selects a registered kind from its subsystem. The type handler
  // runs the subsystem with the kind in the tag name and its own parameters unchanged.


  $padDiagramNamed = $padTag [$pad] !== 'auto';
  $padDiagramKind  = $padDiagramNamed ? $padTag [$pad] : 'flowchart';

  $padTagGo = PAD . 'subs/diagrams/diagram';

  return include PAD . 'types/_go/tag.php';

?>
