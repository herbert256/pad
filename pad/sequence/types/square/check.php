<?php

  function padSeqCheckSquare ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/square/bool.php' ) )
      return padSeqBoolSquare ( $n, $p );

    if ( file_exists ( 'sequence/types/square/generated.php' ) ) 
      return in_array ( $n, PADsquare );

    if ( file_exists ( 'sequence/types/square/fixed.php' ) ) {
      $fixed = include 'sequence/types/square/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence square, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>