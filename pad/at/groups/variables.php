<?php

  // The variables group: look the path $names up in the variables level $padIdx declared
  // on its tag. $padParmParse remembers per name whether it was a level variable ($var,
  // kept in $padSetLvl) or an occurrence variable (%var, whose live value is the global
  // of that name), so the two are gathered into one array before searching. A miss then
  // retries $name as an ordinal into that list and as a plain variable name.

  global $padSetLvl, $padSetOcc, $padParmParse;

  $temp = [];

  foreach ( $padParmParse [$padIdx] as $key => $val )
    if ( $val == 'lvl' ) $temp [$key] = $padSetLvl [$padIdx] [$key];
    else                 $temp [$key] = $GLOBALS [$key];

  $check = padAtSearch ( $temp, $names );
  if ( $check !== INF )
    return $check;

  $key = padAtKey ( $padParmParse [$padIdx], $name );

  if ( $key )
    if ( $padParmParse [$padIdx] [$key] == 'lvl' ) return $padSetLvl [$padIdx] [$key];
    else                                           return $GLOBALS [$key];

  if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl [$padIdx] [$name];
  if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [$name];

  return INF;

?>