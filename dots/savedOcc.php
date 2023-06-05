<?php

  if ( $name )

    for ( $i=0; $i <= $pad; $i++ )

      if ( $padName [$i] == $name ) {

        $current = padDotSearch ( $padSaveOcc [$i], $names, $type );

        if ( $current !== INF ) 
          return $current;

      }

  for ( $i=0; $i <= $pad; $i++ ) {

    $current = padDotSearch ( $padSaveOcc [$i], $names, $type );

    if ( $current !== INF ) 
      return $current;

  }

  return INF;

?>