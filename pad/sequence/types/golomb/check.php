<?php

  function padSeqCheckGolomb ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/golomb/bool.php' ) )
      return padSeqBoolGolomb ( $n, $p );

    if ( file_exists ( 'sequence/types/golomb/generated.php' ) ) 
      return in_array ( $n, PADgolomb );

    if ( file_exists ( 'sequence/types/golomb/fixed.php' ) ) {
      $fixed = include 'sequence/types/golomb/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence golomb, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>