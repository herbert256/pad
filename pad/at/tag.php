<?php

  global $padCurrent, $padData, $padTable, $padOpt, $padPrm, $padSetLvl;

  if ( $kind == 'properties' and file_exists ( "tag/$name.php" ) ) return include pad . "tag/$name.php";
  if ( $kind and file_exists ( "tag/$kind.php" )                 ) return include pad . "tag/$kind.php";

  if ( count ( $names ) == 1 ) {
    if ( isset ( $padCurrent [$padIdx] [$name] ) ) return $padCurrent           [$padIdx] [$name]; 
    if ( isset ( $padOpt     [$padIdx] [$name] ) ) return $padOpt               [$padIdx] [$name]; 
    if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl            [$padIdx] [$name]; 
    if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [ $padSetLvl [$padIdx] [$name] ]; 
    if ( isset ( $padPrm     [$padIdx] [$name] ) ) return $padPrm               [$padIdx] [$name]; 
  }

  $current = padAtSearch ( $padCurrent [$padIdx], $names );
  if ( $current !== INF ) 
    return $current;

  $current = padAtSearch ( $padTable [$padIdx], $names ); 
  if ( $current !== INF ) 
    return $current;

  $current = padAtSearch ( $padSetLvl [$padIdx], $names );
  if ( $current !== INF ) 
    return $current;

  $padOptAt = $padOpt [$padIdx];
  unset ( $padOptAt [0] );

  $current = padAtSearch ( $padOptAt , $names );
  if ( $current !== INF ) 
    return $current;

  $current = padAtSearch ( $padPrm [$padIdx], $names );
  if ( $current !== INF ) 
    return $current;

  $current = padFindNames ( $padCurrent [$padIdx], $names );
  if ( $current !== INF ) 
    return $current;

  $current = padFindNames ( $padData [$padIdx], $names );
  if ( $current !== INF ) 
    return $current;

  if ( count ( $names ) == 1 and file_exists ( pad . "tag/$name.php" ) )
    return include pad . "tag/$name.php";

  return INF;

?>