<?php

  // tree - A hierarchy read from the indentation: a line a box, indented under its parent,
  // drawn top down as boxes joined by lines with rounded turns, each centred over its
  // children; several lines at the least indentation are several roots side by side. The
  // name is the {tree} tag's as well, so bare it is that tag: {diagram:tree} or {diagram
  // type='tree'}. Options: direction (TB, BT, LR, RL), title - Tree unless given.

  include_once PAD . 'subs/diagrams/_lib/tree.php';

  return padDiagramTree ( padDiagramSource (), padDiagramDirection (), (string) padTagParm ( 'title', 'Tree' ) );

?>
