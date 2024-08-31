<?php

  function padSeqCheckIdentity ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/identity/bool.php' ) )
      return padSeqBoolIdentity ( $n );

    if ( file_exists ( '/pad/sequence/types/identity/generated.php' ) ) 
      return in_array ( $n, PADidentity );

    if ( file_exists ( '/pad/sequence/types/identity/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/identity/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence identity, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>