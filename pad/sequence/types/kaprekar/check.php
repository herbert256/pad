<?php

  function padSeqCheckKaprekar ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/kaprekar/bool.php' ) )
      return padSeqBoolKaprekar ( $n, $p );

    if ( file_exists ( 'sequence/types/kaprekar/generated.php' ) ) 
      return in_array ( $n, PADkaprekar );

    if ( file_exists ( 'sequence/types/kaprekar/fixed.php' ) ) {
      $fixed = include 'sequence/types/kaprekar/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence kaprekar, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>