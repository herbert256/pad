<?php

  function pqCheckXor ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/xor/bool.php' ) )
      return pqBoolXor ( $n, $p );

    if ( file_exists ( 'sequence/types/xor/fixed.php' ) ) {
      $fixed = include 'sequence/types/xor/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/xor/generated.php' ) ) 
      return in_array ( $n, PADxor );

    $text = padCode ( "{sequence xor='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>