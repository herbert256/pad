<?php

  function pqCheckNegation ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/negation/bool.php' ) )
      return pqBoolNegation ( $n, $p );

    if ( file_exists ( 'sequence/types/negation/fixed.php' ) ) {
      $fixed = include 'sequence/types/negation/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/negation/generated.php' ) ) 
      return in_array ( $n, PADnegation );

    $text = padCode ( "{sequence negation, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>