<?php

  function padSeqCheckXor ( $f, $n ) {

    if ( file_exists ( 'sequence/types/xor/bool.php' ) )
      return padSeqBoolXor ( $n );

    if ( file_exists ( 'sequence/types/xor/generated.php' ) ) 
      return in_array ( $n, PADxor );

    if ( file_exists ( 'sequence/types/xor/fixed.php' ) ) {
      $fixed = include 'sequence/types/xor/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence xor, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>