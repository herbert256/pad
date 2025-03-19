<?php

  function padSeqCheckMersenne ( $f, $n ) {

    if ( file_exists ( 'seq/types/mersenne/bool.php' ) )
      return padSeqBoolMersenne ( $n );

    if ( file_exists ( 'seq/types/mersenne/generated.php' ) ) 
      return in_array ( $n, PADmersenne );

    if ( file_exists ( 'seq/types/mersenne/fixed.php' ) ) {
      $fixed = include 'seq/types/mersenne/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence mersenne, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>