<?php

  function pqCheckNor ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/nor/bool.php' ) )
      return pqBoolNor ( $n, $p );

    if ( file_exists ( 'sequence/types/nor/fixed.php' ) ) {
      $fixed = include 'sequence/types/nor/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/nor/generated.php' ) ) 
      return in_array ( $n, PADnor );

    $text = padCode ( "{sequence nor='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>