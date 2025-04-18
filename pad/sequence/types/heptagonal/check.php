<?php

  function pqCheckHeptagonal ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/heptagonal/bool.php' ) )
      return pqBoolHeptagonal ( $n, $p );

    if ( file_exists ( 'sequence/types/heptagonal/fixed.php' ) ) {
      $fixed = include 'sequence/types/heptagonal/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/heptagonal/generated.php' ) ) 
      return in_array ( $n, PADheptagonal );

    $text = padCode ( "{sequence heptagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>