<?php

  global $padCurrent, $padData, $padTable, $padOpt, $padPrm, $padSetLvl;

  $name = end ( $names );

  if ( count ( $names ) == 1 ) {
    if ( isset ( $padCurrent [$padIdx] [$name] ) ) return $padCurrent [$padIdx] [$name]; 
    if ( isset ( $padOpt     [$padIdx] [$name] ) ) return $padOpt     [$padIdx] [$name]; 
    if ( isset ( $padSetLvl  [$padIdx] [$name] ) ) return $padSetLvl  [$padIdx] [$name]; 
    if ( isset ( $padPrm     [$padIdx] [$name] ) ) return $padPrm     [$padIdx] [$name]; 
    if ( isset ( $padSetOcc  [$padIdx] [$name] ) ) return $GLOBALS [$name]; 
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

  $current = padAtSearchNames ( $padCurrent [$padIdx], $names );
  if ( $current !== INF ) 
    return $current;

  $current = padAtSearchNames ( $padData [$padIdx], $names );
  if ( $current !== INF ) 
    return $current;

  if ( count ( $names ) == 1 and file_exists ( pad . "at/properties/$name.php" ) )
    return include pad . "at/properties/$name.php";

  return INF;

?>