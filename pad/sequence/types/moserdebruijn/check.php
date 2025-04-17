<?php

  function pqCheckMoserdebruijn ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/moserdebruijn/bool.php' ) )
      return pqBoolMoserdebruijn ( $n, $p );

    if ( file_exists ( 'sequence/types/moserdebruijn/fixed.php' ) ) {
      $fixed = include 'sequence/types/moserdebruijn/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/moserdebruijn/generated.php' ) ) 
      return in_array ( $n, PADmoserdebruijn );

    $text = padCode ( "{sequence moserdebruijn, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>