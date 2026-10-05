<?php

  // The depth@tag property: how deep level $padIdx stands in a recursion - for a {tree},
  // 1 for its own rows and one more per {recurse}, for indenting a tree. Any other level
  // counts the levels of its own name from the top down to itself, so a tag that calls
  // itself has a depth as well, and a level that does not is at depth 1.

  global $padLevelId, $padName, $padTreeStore;

  if ( isset ( $padTreeStore [ $padLevelId [$padIdx] ?? 0 ] ) )
    return $padTreeStore [ $padLevelId [$padIdx] ] ['depth'];

  $padDepthCount = 0;

  for ( $padDepthI = 1; $padDepthI <= $padIdx; $padDepthI++ )
    if ( ( $padName [$padDepthI] ?? '' ) === $padName [$padIdx] )
      $padDepthCount++;

  return $padDepthCount;

?>
