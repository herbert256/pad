<?php

  function padSeqCheckNand ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/nand/bool.php' ) )
      return padSeqBoolNand ( $n, $p );

    if ( file_exists ( 'sequence/types/nand/generated.php' ) ) 
      return in_array ( $n, PADnand );

    if ( file_exists ( 'sequence/types/nand/fixed.php' ) ) {
      $fixed = include 'sequence/types/nand/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence nand='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>