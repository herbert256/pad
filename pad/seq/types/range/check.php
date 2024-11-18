<?php

  function padSeqCheckRange ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/range/bool.php' ) )
      return padSeqBoolRange ( $n );

    if ( file_exists ( PAD . 'seq/types/range/generated.php' ) ) 
      return in_array ( $n, PADrange );

    if ( file_exists ( PAD . 'seq/types/range/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/range/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq range, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>