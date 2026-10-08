<?php

  // {diagram}...{/diagram} - a flowchart or a sequence diagram from a few lines of text in
  // the content, laid out on the server by lib/diagram.php and written as inline SVG. The
  // lines are written the Mermaid way:
  //
  //   {diagram direction='LR'}             {diagram type='sequence'}
  //     A[Order] --> B{Paid?}                Shop ->> Bank: Charge
  //     B -->|yes| C((Ship))                 Bank -->> Shop: Approved
  //     B -.->|no| D(Remind) --> B         {/diagram}
  //   {/diagram}
  //
  // type= is flowchart (the default) or sequence - a first line sequenceDiagram says so
  // too; direction= is TB (the default), BT, LR or RL, else a first line graph LR names
  // it; edges= is orthogonal (the default), straight or curved; title= names the picture
  // for a screen reader. The content is taken as it stands, before the level walks it, so
  // the braces of a decision B{Paid?} need no {ignore}. A diagram without nodes answers
  // nothing, and its @else@ shows.

  $padDiagramType = strtolower ( trim ( (string) padTagParm ( 'type', 'flowchart' ) ) );
  $padDiagramDir  = strtoupper ( trim ( (string) padTagParm ( 'direction', '' ) ) );
  $padDiagramForm = strtolower ( trim ( (string) padTagParm ( 'edges', 'orthogonal' ) ) );
  $padDiagramText = padChartDedent ( $padContent );
  $padContent     = '';

  if ( preg_match ( '/^\s*sequenceDiagram\b/', $padDiagramText ) )
    $padDiagramType = 'sequence';

  if ( ! in_array ( $padDiagramType, [ 'flowchart', 'flow', 'graph', 'sequence' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the diagram has no type '" . padMakeSafe ( $padDiagramType, 20 ) . "' - flowchart or sequence" );
    $padDiagramType = 'flowchart';
  }

  if ( ! in_array ( $padDiagramDir, [ '', 'TB', 'TD', 'BT', 'LR', 'RL' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the diagram has no direction '" . padMakeSafe ( $padDiagramDir, 20 ) . "' - TB, BT, LR or RL" );
    $padDiagramDir = '';
  }

  if ( ! in_array ( $padDiagramForm, [ 'orthogonal', 'straight', 'curved' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the diagram has no edges '" . padMakeSafe ( $padDiagramForm, 20 ) . "' - orthogonal, straight or curved" );
    $padDiagramForm = 'orthogonal';
  }

  if ( $padDiagramType == 'sequence' )
    $padDiagramSvg = padDiagramSequence ( $padDiagramText, (string) padTagParm ( 'title', 'Sequence diagram' ) );
  else
    $padDiagramSvg = padDiagramFlow ( $padDiagramText, $padDiagramDir, $padDiagramForm, (string) padTagParm ( 'title', 'Flowchart' ) );

  return ( $padDiagramSvg === '' ) ? FALSE : $padDiagramSvg;

?>
