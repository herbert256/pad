<?php

  function pqCheckStrong ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/strong/bool.php' ) )
      return pqBoolStrong ( $n, $p );

    if ( file_exists ( 'sequence/types/strong/fixed.php' ) ) {
      $fixed = include 'sequence/types/strong/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/strong/generated.php' ) ) 
      return in_array ( $n, PADstrong );

    $text = padCode ( "{sequence strong, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>