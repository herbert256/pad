<?php

  function padSeqCheckNand ( $f, $n ) {

    if ( file_exists ( 'seq/types/nand/bool.php' ) )
      return padSeqBoolNand ( $n );

    if ( file_exists ( 'seq/types/nand/generated.php' ) ) 
      return in_array ( $n, PADnand );

    if ( file_exists ( 'seq/types/nand/fixed.php' ) ) {
      $fixed = include 'seq/types/nand/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence nand, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>