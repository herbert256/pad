<?php

  function pqCheckSquare ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/square/bool.php' ) )
      return pqBoolSquare ( $n, $p );

    if ( file_exists ( 'sequence/types/square/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/square/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/square/generated.php' ) ) 
      return in_array ( $n, PADsquare );

    #$text = padCode ( "{sequence square, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence square, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>