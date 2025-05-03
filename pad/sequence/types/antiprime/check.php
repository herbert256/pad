<?php

  function pqCheckAntiprime ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/antiprime/bool.php' ) )
      return pqBoolAntiprime ( $n, $p );

    if ( file_exists ( 'sequence/types/antiprime/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/antiprime/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/antiprime/generated.php' ) ) 
      return in_array ( $n, PADantiprime );

    #$text = padCode ( "{sequence antiprime, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence antiprime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>