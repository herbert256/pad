<?php

  function padSeqCheckHexagonal ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/hexagonal/bool.php' ) )
      return padSeqBoolHexagonal ( $n );

    if ( file_exists ( PAD . 'seq/types/hexagonal/generated.php' ) ) 
      return in_array ( $n, PADhexagonal );

    if ( file_exists ( PAD . 'seq/types/hexagonal/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/hexagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq hexagonal, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>