<?php

  function pqCheckNand ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/nand/bool.php' ) )
      return pqBoolNand ( $n, $p );

    if ( file_exists ( 'sequence/types/nand/fixed.php' ) ) {
      $fixed = include 'sequence/types/nand/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/nand/generated.php' ) ) 
      return in_array ( $n, PADnand );

    $text = padCode ( "{sequence nand='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>