<?php

  // Type handler for a pattern: {pattern:topography seed=7/}, the pattern: prefix naming a file
  // of subs/patterns/types/ - never a bare name, the patterns' names (dots, grid, waves) being
  // those of fields and pages. The kind is the tag's name, and it is drawn as
  // {pattern 'topography', seed=7/} draws it, by subs/patterns/pattern.php - run as a built-in
  // tag's PHP half is run, types/_go/tag.php.

  $padPatternKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/patterns/pattern';

  return include PAD . 'types/_go/tag.php';

?>
