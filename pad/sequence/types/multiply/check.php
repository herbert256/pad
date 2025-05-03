<?php

  function pqCheckMultiply ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/multiply/bool.php' ) )
      return pqBoolMultiply ( $n, $p );

    if ( file_exists ( 'sequence/types/multiply/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/multiply/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/multiply/generated.php' ) ) 
      return in_array ( $n, PADmultiply );

    #$text = padCode ( "{sequence multiply='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence multiply='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>