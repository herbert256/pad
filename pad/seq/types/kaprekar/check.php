<?php

  function padSeqCheckKaprekar ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/kaprekar/bool.php' ) )
      return padSeqBoolKaprekar ( $n );

    if ( file_exists ( PAD . 'seq/types/kaprekar/generated.php' ) ) 
      return in_array ( $n, PADkaprekar );

    if ( file_exists ( PAD . 'seq/types/kaprekar/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/kaprekar/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq kaprekar, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>