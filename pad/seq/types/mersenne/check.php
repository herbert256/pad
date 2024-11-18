<?php

  function padSeqCheckMersenne ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/mersenne/bool.php' ) )
      return padSeqBoolMersenne ( $n );

    if ( file_exists ( PAD . 'seq/types/mersenne/generated.php' ) ) 
      return in_array ( $n, PADmersenne );

    if ( file_exists ( PAD . 'seq/types/mersenne/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/mersenne/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq mersenne, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>