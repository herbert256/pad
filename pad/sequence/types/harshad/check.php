<?php

  function padSeqCheckHarshad ( $f, $n ) {

    if ( file_exists ( 'sequence/types/harshad/bool.php' ) )
      return padSeqBoolHarshad ( $n );

    if ( file_exists ( 'sequence/types/harshad/generated.php' ) ) 
      return in_array ( $n, PADharshad );

    if ( file_exists ( 'sequence/types/harshad/fixed.php' ) ) {
      $fixed = include 'sequence/types/harshad/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence harshad, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>