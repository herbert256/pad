<?php

  // {pad:theme 'paper'/} - the page in a theme of subs/themes/types/: light, dark, paper, terminal,
  // newspaper, contrast, contrast-dark, sepia, ocean, forest, mono ... - the frame's colours swapped, by the
  // themes subsystem, subs/themes/theme.php. $padTheme sets one for every page of an
  // application, and {pad:themes/} lets the visitor choose.

  return include PAD . 'subs/themes/theme.php';

?>
