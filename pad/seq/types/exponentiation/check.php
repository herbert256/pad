<?php

  function padSeqCheckExponentiation ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/exponentiation/bool.php' ) )
      return padSeqBoolExponentiation ( $n );

    if ( file_exists ( PAD . 'seq/types/exponentiation/generated.php' ) ) 
      return in_array ( $n, PADexponentiation );

    if ( file_exists ( PAD . 'seq/types/exponentiation/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/exponentiation/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq exponentiation, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>