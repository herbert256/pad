<?php

  // sequence - Participants side by side, a lifeline under each, the messages between them
  // from top to bottom: participant A as Alice, actor U, A ->> B: text, B -->> A: reply,
  // Note over A,B: text, the frames loop, alt / else, opt, par / and, critical and break
  // closed by end, and autonumber. A first line sequenceDiagram names the kind. The name is
  // the sequence tag's as well, so bare it is that tag: {diagram:sequence} or {diagram
  // type='sequence'}. Options: title - Sequence diagram unless given.

  include_once PAD . 'subs/diagrams/_lib/sequence.php';

  return padDiagramSequence ( padDiagramSource (), (string) padTagParm ( 'title', 'Sequence diagram' ) );

?>
