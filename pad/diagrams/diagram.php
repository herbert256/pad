<?php

  // The body of a diagram, whichever way it is written: {diagram type='sequence'}...{/diagram},
  // the diagram: type - {diagram:sequence}...{/diagram:sequence} - or a bare
  // {flowchart}...{/flowchart} when nothing else has the name. The caller sets
  // $padDiagramKind, and $padDiagramNamed when the tag names it; a kind is a file of
  // diagrams/types/, and the files there are all the kinds there are (padDiagramTypes,
  // diagrams/_lib/diagram.php). A first line written the Mermaid way - sequenceDiagram,
  // stateDiagram - names the kind too (padDiagramHeader).
  //
  // The content is taken as it stands, before the level walks it, so the braces of a
  // decision B{Paid?} need no {ignore}; the level is left no content of its own to render.
  // A diagram without nodes answers nothing, and its @else@ shows.

  include_once PAD . 'diagrams/_lib/diagram.php';

  $padDiagramText = padChartDedent ( $padContent );
  $padContent     = '';

  // A first line that is the kind's name alone - mindmap - said which kind it is and is no
  // line of it: it is left blank, so the lines after it keep their numbers. A name with
  // Diagram or Graph is a line the kind reads itself, as the sequence diagram always has.

  if ( ( $padDiagramHead = padDiagramHeader ( $padDiagramText, $padDiagramNamed ?? TRUE ) ) !== '' ) {
    $padDiagramKind = $padDiagramHead;
    $padDiagramText = preg_replace ( '/^(\s*)' . $padDiagramHead . '[ \t]*(?=\r?\n|$)/', '$1', $padDiagramText, 1 );
  }

  if ( ! padDiagramType ( $padDiagramKind ) ) {
    if ( $padCheckSyntax )
      padError ( "the diagram has no type '" . padMakeSafe ( $padDiagramKind, 20 ) . "' - " . implode ( ', ', padDiagramTypes () ) );
    $padDiagramKind = 'flowchart';
  }

  // The kind drawn goes on the xref record, which the reference application shows: the pages
  // that draw a diagram of each kind.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'diagram', $padDiagramKind );

  $padDiagramSvg = padDiagramTag ( $padDiagramKind, $padDiagramText );

  return ( $padDiagramSvg === '' ) ? FALSE : $padDiagramSvg;

?>
