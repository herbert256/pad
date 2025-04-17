<?php

  function pqCheckIdentity ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/identity/bool.php' ) )
      return pqBoolIdentity ( $n, $p );

    if ( file_exists ( 'sequence/types/identity/fixed.php' ) ) {
      $fixed = include 'sequence/types/identity/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/identity/generated.php' ) ) 
      return in_array ( $n, PADidentity );

    $text = padCode ( "{sequence identity, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>