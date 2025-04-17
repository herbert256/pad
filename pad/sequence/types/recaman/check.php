<?php

  function pqCheckRecaman ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/recaman/bool.php' ) )
      return pqBoolRecaman ( $n, $p );

    if ( file_exists ( 'sequence/types/recaman/fixed.php' ) ) {
      $fixed = include 'sequence/types/recaman/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/recaman/generated.php' ) ) 
      return in_array ( $n, PADrecaman );

    $text = padCode ( "{sequence recaman, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>