<?php

  function padSeqCheckHarshad ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/harshad/bool.php' ) )
      return padSeqBoolHarshad ( $n, $p );

    if ( file_exists ( 'sequence/types/harshad/fixed.php' ) ) {
      $fixed = include 'sequence/types/harshad/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/harshad/generated.php' ) ) 
      return in_array ( $n, PADharshad );

    $text = padCode ( "{sequence harshad, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>