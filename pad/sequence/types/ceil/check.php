<?php

  function pqCheckCeil ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/ceil/bool.php' ) )
      return pqBoolCeil ( $n, $p );

    if ( file_exists ( 'sequence/types/ceil/fixed.php' ) ) {
      $fixed = include 'sequence/types/ceil/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/ceil/generated.php' ) ) 
      return in_array ( $n, PADceil );

    $text = padCode ( "{sequence ceil='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>