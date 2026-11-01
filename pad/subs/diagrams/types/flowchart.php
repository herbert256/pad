<?php

  // flowchart - Nodes and the edges between them, a line a chain: A[Box] --> B{Decision?},
  // B -->|label| C((Circle)), A & B --> C; laid out in layers, as few crossings as it can
  // find. A first line graph LR or flowchart LR names the direction. Options: direction (TB,
  // BT, LR, RL), edges (orthogonal, straight, curved), title - Flowchart unless given.

  return padDiagramFlow ( padDiagramSource (), padDiagramDirection (), padDiagramEdges (),
                          (string) padTagParm ( 'title', 'Flowchart' ) );

?>
