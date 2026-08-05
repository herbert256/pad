<?php

  // The current group: look the path $names up in the occurrence the level $padIdx is
  // presently on, i.e. the row a {tag} loop has in hand. INF when that level has no
  // current occurrence.

  global $padCurrent;

  if ( ! isset ($padCurrent [$padIdx]) )
    return INF;

  return padAtSearch ( $padCurrent [$padIdx], $names );

?>