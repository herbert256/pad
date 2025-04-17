<?php

  function pqCheckRange ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/range/bool.php' ) )
      return pqBoolRange ( $n, $p );

    if ( file_exists ( 'sequence/types/range/fixed.php' ) ) {
      $fixed = include 'sequence/types/range/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/range/generated.php' ) ) 
      return in_array ( $n, PADrange );

    $text = padCode ( "{sequence range, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>