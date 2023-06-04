<?php

  if ( $first )
    for ( $i=$pad; $i >= 0; $i-- ) 
      if ( $padName [$i] == $name ) {
        $current = padDotSearch ( $padPrm [$i], [ 0 => $first ], $type );
        if ($current !== INF )
          return $current;
      }

  for ( $i=$pad; $i >= 0; $i-- ) {
    $current = padDotSearch ( $padPrm [$i], [ 0 => $name ], $type );
    if ($current !== INF )
      return $current;
  } 
 
  return INF;

?>