<?php

  if ( $name )

    for ( $i=$pad; $i >= 0; $i-- )

      if ( $padName [$i] == $name ) {

        $current = padDotSearch ( $padSetLvl [$i], $names, $type );

        if ( $current !== INF ) 
          return $current;

      }

  for ( $i=$pad; $i >= 0; $i-- ) {

    $current = padDotSearch ( $padSetLvl [$i], $names, $type );

    if ( $current !== INF ) 
      return $current;

  }

  return INF;

?>