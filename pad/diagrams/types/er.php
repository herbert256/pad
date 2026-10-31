<?php

  // er - Entities as tables of their attributes and the relations between them, the feet of
  // a crow at each end for how many, the Mermaid way: CUSTOMER ||--o{ ORDER : places, the
  // ends || (exactly one), |o (zero or one), }| (one or more), }o (zero or more) - mirrored on
  // the right - -- identifying and .. not; CUSTOMER { string name PK "comment" } an
  // attribute a line. Laid out in layers as a flowchart is. A first line erDiagram names the
  // kind, direction LR the direction. Options: direction (TB, BT, LR, RL), edges
  // (orthogonal, straight, curved), title - Entity-relationship diagram unless given.

  include_once PAD . 'diagrams/_lib/er.php';

  return padDiagramEr ( padDiagramSource (), padDiagramDirection (), padDiagramEdges (),
                        (string) padTagParm ( 'title', 'Entity-relationship diagram' ) );

?>
