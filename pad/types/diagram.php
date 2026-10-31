<?php

  // Type handler for a diagram kind: {diagram:sequence}...{/diagram:sequence}, or a bare pair
  // naming a file of diagrams/types/ when nothing else has the name, {flowchart}...{/flowchart}.
  // The kind is the tag's name, and the diagram is drawn as {diagram type='sequence'} draws
  // it, by diagrams/diagram.php - run as a built-in tag's PHP half is run, types/_go/tag.php.

  $padDiagramKind  = $padTag [$pad];
  $padDiagramNamed = TRUE;

  $padTagGo = PAD . 'diagrams/diagram';

  return include PAD . 'types/_go/tag.php';

?>
