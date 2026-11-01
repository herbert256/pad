<?php

  // mindmap - A subject in the middle and its branches round it, the hierarchy read from the
  // indentation, the Mermaid way: the first line the root, every line indented under its
  // parent, a shape by its brackets - [square], (rounded), ((circle)), {{hexagon}}, )cloud(,
  // ))bang(( - or none. The root's branches are shared over its two sides, each a colour
  // its twigs keep. A first line mindmap names the kind. Options: title - Mind map unless
  // given.

  include_once PAD . 'subs/diagrams/_lib/mindmap.php';

  return padDiagramMindmap ( padDiagramSource (), (string) padTagParm ( 'title', 'Mind map' ) );

?>
