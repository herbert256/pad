<?php

  if ( $name )

    for ( $i=0; $i <= $pad; $i++ )

      if ( $padName [$i] == $name ) {

        $current = padAtSearch ( $padSaveLvl [$i], $names );

        if ( $current !== INF ) 
          return $current;

      }

  for ( $i=0; $i <= $pad; $i++ ) {

    $current = padAtSearch ( $padSaveLvl [$i], $names );

    if ( $current !== INF ) 
      return $current;

  }

  return INF;

?>