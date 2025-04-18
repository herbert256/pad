<?php

  function pqCheckLoop ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/loop/bool.php' ) )
      return pqBoolLoop ( $n, $p );

    if ( file_exists ( 'sequence/types/loop/fixed.php' ) ) {
      $fixed = include 'sequence/types/loop/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/loop/generated.php' ) ) 
      return in_array ( $n, PADloop );

    $text = padCode ( "{sequence loop, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>