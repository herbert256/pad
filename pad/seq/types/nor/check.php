<?php

  function padSeqCheckNor ( $f, $n ) {

    if ( file_exists ( 'seq/types/nor/bool.php' ) )
      return padSeqBoolNor ( $n );

    if ( file_exists ( 'seq/types/nor/generated.php' ) ) 
      return in_array ( $n, PADnor );

    if ( file_exists ( 'seq/types/nor/fixed.php' ) ) {
      $fixed = include 'seq/types/nor/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq nor, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>