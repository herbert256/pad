<?php

  function pqCheckCaterer ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/caterer/bool.php' ) )
      return pqBoolCaterer ( $n, $p );

    if ( file_exists ( 'sequence/types/caterer/fixed.php' ) ) {
      $fixed = include 'sequence/types/caterer/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/caterer/generated.php' ) ) 
      return in_array ( $n, PADcaterer );

    $text = padCode ( "{sequence caterer, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>