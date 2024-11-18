<?php

  function padSeqCheckTriangular ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/triangular/bool.php' ) )
      return padSeqBoolTriangular ( $n );

    if ( file_exists ( PAD . 'seq/types/triangular/generated.php' ) ) 
      return in_array ( $n, PADtriangular );

    if ( file_exists ( PAD . 'seq/types/triangular/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/triangular/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq triangular, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>