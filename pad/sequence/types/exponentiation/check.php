<?php

  function padSeqCheckExponentiation ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/exponentiation/bool.php' ) )
      return padSeqBoolExponentiation ( $n, $p );

    if ( file_exists ( 'sequence/types/exponentiation/fixed.php' ) ) {
      $fixed = include 'sequence/types/exponentiation/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/exponentiation/generated.php' ) ) 
      return in_array ( $n, PADexponentiation );

    $text = padCode ( "{sequence exponentiation='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>