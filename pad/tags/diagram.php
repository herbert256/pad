<?php

  // {diagram}...{/diagram} - a diagram from a few lines of text in the content, written the
  // Mermaid way, laid out on the server by the diagram subsystem in diagrams/ and written as
  // inline SVG. type= names the kind, a file of diagrams/types/ - the flowchart unless it
  // says otherwise - the same diagram as the diagram: type draws, {diagram:sequence}:
  //
  //   {diagram direction='LR'}             {diagram type='sequence'}
  //     A[Order] --> B{Paid?}                Shop ->> Bank: Charge
  //     B -->|yes| C((Ship))                 Bank -->> Shop: Approved
  //     B -.->|no| D(Remind) --> B         {/diagram}
  //   {/diagram}
  //
  // flow and graph, the words Mermaid starts a flowchart with, name the flowchart as well.

  $padDiagramKind = strtolower ( trim ( (string) padTagParm ( 'type', 'flowchart' ) ) );

  if ( $padDiagramKind == 'flow' or $padDiagramKind == 'graph' )
    $padDiagramKind = 'flowchart';

  return include PAD . 'diagrams/diagram.php';

?>
