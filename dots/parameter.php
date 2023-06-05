<?php


  if ( $name )

    for ( $i=$pad; $i >= 0; $i-- )

      if ( $padName [$i] == $name ) {

        $padOptDot = $padOpt [$i];
        unset ( $padOptDot [0] );  

        $current = padDotSearch ( $padOptDot, $names, $type );

        if ( $current !== INF ) 
          return $current;

      }


  for ( $i=$pad; $i >= 0; $i-- ) {

    $padOptDot = $padOpt [$i];
    unset ( $padOptDot [0] );  

    $current = padDotSearch ( $padOptDot, $names, $type );

    if ( $current !== INF ) 
      return $current;

  }


  return INF;


?>