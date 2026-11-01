<?php

  // state - The states of a machine and the transitions between them, the Mermaid way:
  // [*] --> Idle, Idle --> Running : start, Running --> [*], state "Long name" as L,
  // L : a line of description, state C <<choice>>, <<fork>> and <<join>>; laid out in
  // layers as a flowchart is. A first line stateDiagram or stateDiagram-v2 names the kind,
  // direction LR the direction. Options: direction (TB, BT, LR, RL), edges (orthogonal,
  // straight, curved), title - State diagram unless given.

  include_once PAD . 'subs/diagrams/_lib/state.php';

  return padDiagramState ( padDiagramSource (), padDiagramDirection (), padDiagramEdges (),
                           (string) padTagParm ( 'title', 'State diagram' ) );

?>
