<?php

  function pqCheckOctagonal ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/octagonal/bool.php' ) )
      return pqBoolOctagonal ( $n, $p );

    if ( file_exists ( 'sequence/types/octagonal/fixed.php' ) ) {
      $fixed = include 'sequence/types/octagonal/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/octagonal/generated.php' ) ) 
      return in_array ( $n, PADoctagonal );

    $text = padCode ( "{sequence octagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>