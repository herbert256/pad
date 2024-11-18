<?php

  function padSeqCheckSquare ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/square/bool.php' ) )
      return padSeqBoolSquare ( $n );

    if ( file_exists ( PAD . 'seq/types/square/generated.php' ) ) 
      return in_array ( $n, PADsquare );

    if ( file_exists ( PAD . 'seq/types/square/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/square/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq square, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>