<?php

  // A diagram uses {diagram:sequence}...{/diagram:sequence} or another registered
  // kind in subs/diagrams/types/. The caller sets $padDiagramKind and $padDiagramNamed;
  // {diagram:auto} detects a Mermaid header and otherwise defaults to flowchart.
  //
  // The content is taken as it stands, before the level walks it, so the braces of a
  // decision B{Paid?} need no {pad:ignore}; the level is left no content of its own to render.
  // A diagram without nodes answers nothing, and its @else@ shows.

  include_once PAD . 'subs/diagrams/_lib/diagram.php';

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
