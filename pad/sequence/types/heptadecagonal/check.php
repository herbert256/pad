<?php

  function pqCheckHeptadecagonal ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/heptadecagonal/bool.php' ) )
      return pqBoolHeptadecagonal ( $n, $p );

    if ( file_exists ( 'sequence/types/heptadecagonal/fixed.php' ) ) {
      $fixed = include 'sequence/types/heptadecagonal/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/heptadecagonal/generated.php' ) ) 
      return in_array ( $n, PADheptadecagonal );

    $text = padCode ( "{sequence heptadecagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>