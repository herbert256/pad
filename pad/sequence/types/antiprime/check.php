<?php

  function padSeqCheckAntiprime ( $f, $n ) {

    if ( file_exists ( 'sequence/types/antiprime/bool.php' ) )
      return padSeqBoolAntiprime ( $n );

    if ( file_exists ( 'sequence/types/antiprime/generated.php' ) ) 
      return in_array ( $n, PADantiprime );

    if ( file_exists ( 'sequence/types/antiprime/fixed.php' ) ) {
      $fixed = include 'sequence/types/antiprime/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence antiprime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>