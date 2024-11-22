<?php

  function padSeqCheckIdentity ( $f, $n ) {

    if ( file_exists ( 'seq/types/identity/bool.php' ) )
      return padSeqBoolIdentity ( $n );

    if ( file_exists ( 'seq/types/identity/generated.php' ) ) 
      return in_array ( $n, PADidentity );

    if ( file_exists ( 'seq/types/identity/fixed.php' ) ) {
      $fixed = include 'seq/types/identity/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq identity, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>