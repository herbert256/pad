<?php

  if ( $second and padExists ( pad . "dots/tag/$first.php" ) ) 
    return include pad . "dots/tag/$first.php";

  $current = padDotSearch ( $padCurrent [$i], $names, $type ); 
  if ( $current !== INF ) 
    return $current;

  $current = padDotSearch ( $padTable [$i], $names, $type ); 
  if ( $current !== INF ) 
    return $current;

  $current = padDotSearch ( $padPrm [$i], [ 0 => $first ], $type ); 
  if ( $current !== INF ) 
    return $current;

  $current = padDotSearch ( $padOpt [$i], [ 0 => $first ], $type ); 
  if ( $current !== INF ) 
    return $current;

  $current = padDotSearch ( $padSetLvl [$i], [ 0 => $first ], $type ); 
  if ( $current !== INF ) 
    return $current;

  return INF;

?>