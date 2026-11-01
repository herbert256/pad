<?php

  // railroad - The rules of a grammar as syntax diagrams, a track a rule, written in the EBNF
  // of the W3C (XML 1.0, section 6 Notation), the notation the railroad diagram generators
  // read: name ::= expression, a rule a line or more - "text" or 'text' a terminal in a
  // rounded box, a name a rule in a square one (a link to the rule when the grammar has it),
  // [a-z] and #x20 a character class, ( ) a group, A | B a choice, A? optional, A* none or
  // more, A+ one or more, A - B A but not B, /* comments */. A choice stacks its branches
  // under each other, optional passes over, a repetition loops back under; a rule wider than
  // the track wraps to a next row. A first line railroad names the kind. Options: title -
  // Railroad diagram unless given.

  include_once PAD . 'subs/diagrams/_lib/railroad.php';

  return padDiagramRailroad ( padDiagramSource (), (string) padTagParm ( 'title', 'Railroad diagram' ) );

?>
