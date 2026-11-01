<?php

  // class - UML classes, a box each of the name, the fields and the methods, and the
  // relations between them, the Mermaid way: class A { +String name; +move() bool } a member
  // a line, A : +member, <<interface>> A, A <|-- B (B inherits from A), A <|.. B (implements),
  // A *-- B (made of), A o-- B (holds), A "1" --> "*" B : label, A ..> B (depends on), A -- B;
  // laid out in layers, the parent and the whole above. A first line classDiagram names the
  // kind, direction LR the direction. The name is a common one: a field or a store called
  // class wins over it bare. Options: direction (TB, BT, LR, RL), edges (orthogonal,
  // straight, curved), title - Class diagram unless given.

  include_once PAD . 'subs/diagrams/_lib/class.php';

  return padDiagramClass ( padDiagramSource (), padDiagramDirection (), padDiagramEdges (),
                           (string) padTagParm ( 'title', 'Class diagram' ) );

?>
