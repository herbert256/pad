<?php

  function padSeqCheckGnomonic ( $f, $n ) {

    if ( file_exists ( 'seq/types/gnomonic/bool.php' ) )
      return padSeqBoolGnomonic ( $n );

    if ( file_exists ( 'seq/types/gnomonic/generated.php' ) ) 
      return in_array ( $n, PADgnomonic );

    if ( file_exists ( 'seq/types/gnomonic/fixed.php' ) ) {
      $fixed = include 'seq/types/gnomonic/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence gnomonic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>