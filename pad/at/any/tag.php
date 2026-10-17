<?php

  // Everything-at-one-level lookup: resolve the path $names against level $padIdx by
  // trying that level's stores in turn. Included by padAtTag() for a plain names@level
  // reference, by the any group, and level by level by at/any/tags.php.
  //
  // A single-name path is answered straight from the current occurrence row, the level's
  // positional parameters, its level variables, its named options or an occurrence
  // variable (whose value lives in $GLOBALS). A longer path is searched with padAtSearch
  // in roughly that order, then through the level's function variables, its iteration
  // properties and finally its whole data set. Returns INF when $padIdx is FALSE or
  // nothing matched.

  if ( ! $padIdx )
    return INF;

  global $padCurrent, $padData, $padOpt, $padPrm, $padSetLvl, $padLvlFunVar, $padSetOcc, $padAtShallow;

  // A plain dotted path - $user.name, no @ - is looked for from the top of each store only
  // (padFieldAt): searched in depth, a row without it took a sibling row's.

  $padAtNoDeep = ( $padAtShallow ?? FALSE ) ? 1 : 0;

  $name = end ( $names );

  if ( count ( $names ) == 1 ) {
    if ( isset ( $padCurrent [$padIdx] [$name] ) ) return $padCurrent [$padIdx] [$name];
    if ( isset ( $padOpt     [$padIdx] [$name] ) ) return $padOpt     [$padIdx] [$name];
    if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl  [$padIdx] [$name];
    if ( isset ( $padPrm     [$padIdx] [$name] ) ) return $padPrm     [$padIdx] [$name];
    if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [$name];
  }

  $current = padAtSearch ( $padCurrent [$padIdx], $names, $padAtNoDeep );
  if ( $current !== INF )
    return $current;

  $current = padAtSearch ( $padSetLvl [$padIdx], $names, $padAtNoDeep );
  if ( $current !== INF )
    return $current;

  $padOptAt = $padOpt [$padIdx];
  unset ( $padOptAt [0] );
  $current = padAtSearch ( $padOptAt , $names, $padAtNoDeep );
  if ( $current !== INF )
    return $current;

  $current = padAtSearch ( $padPrm [$padIdx], $names, $padAtNoDeep );
  if ( $current !== INF )
    return $current;

  $current = padAtSearch ( $padLvlFunVar [$padIdx], $names, $padAtNoDeep );
  if ( $current !== INF )
    return $current;

  $current = padAtProperty ($names, $padIdx );
  if ( $current !== INF )
    return $current;

  $current = padAtSearch ( $padData [$padIdx], $names, $padAtNoDeep );
  if ( $current !== INF )
    return $current;

  return INF;

?>
