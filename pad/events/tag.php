<?php

  // Records the current tag in the xref report under its type. Nothing includes this file at
  // present - events/levelStart.php makes the same call inline, after stripping any @property
  // suffix from the tag name.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref ( 'tag', $padType [$pad], $padTag [$pad] );

?>