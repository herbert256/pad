<?php

  if ( $name )
    
    for ( $i=$pad; $i >= 0; $i-- )

      if ( $padName [$i] == $name ) {

        $current = include pad . "dots/go/tag.php";
        
        if ( $current !== INF ) 
          return $current;

      }

  for ( $i=$pad; $i >= 0; $i-- ) {

    $current = include pad . "dots/go/tag.php";

    if ( $current !== INF ) 
      return $current;

  }

  return INF;

?>