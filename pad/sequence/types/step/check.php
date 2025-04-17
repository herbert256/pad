<?php

  function pqCheckStep ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/step/bool.php' ) )
      return pqBoolStep ( $n, $p );

    if ( file_exists ( 'sequence/types/step/fixed.php' ) ) {
      $fixed = include 'sequence/types/step/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/step/generated.php' ) ) 
      return in_array ( $n, PADstep );

    $text = padCode ( "{sequence step='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>