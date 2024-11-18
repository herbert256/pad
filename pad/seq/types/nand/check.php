<?php

  function padSeqCheckNand ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/nand/bool.php' ) )
      return padSeqBoolNand ( $n );

    if ( file_exists ( PAD . 'seq/types/nand/generated.php' ) ) 
      return in_array ( $n, PADnand );

    if ( file_exists ( PAD . 'seq/types/nand/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/nand/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq nand, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>