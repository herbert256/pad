<?php

  // auto - Select a diagram from its Mermaid header, or draw a flowchart without one.
  // {diagram:auto}sequenceDiagram ...{/diagram:auto}; explicit kinds remain available.

  $padAutoSource = padDiagramSource ();
  $padAutoKind = padDiagramHeader ( $padAutoSource, FALSE ) ?: 'flowchart';
  $padAutoSource = preg_replace ( '/^(\s*)' . $padAutoKind . '[ \t]*(?=\r?\n|$)/', '$1', $padAutoSource, 1 );
  return padDiagramTag ( $padAutoKind, $padAutoSource );

?>
