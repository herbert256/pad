<?php

  function pqCheckRandom ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/random/bool.php' ) )
      return pqBoolRandom ( $n, $p );

    if ( file_exists ( 'sequence/types/random/fixed.php' ) ) {
      $fixed = include 'sequence/types/random/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/random/generated.php' ) ) 
      return in_array ( $n, PADrandom );

    $text = padCode ( "{sequence random='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>