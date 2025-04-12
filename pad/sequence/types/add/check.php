<?php

  function padSeqCheckAdd ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/add/bool.php' ) )
      return padSeqBoolAdd ( $n, $p );

    if ( file_exists ( 'sequence/types/add/generated.php' ) ) 
      return in_array ( $n, PADadd );

    if ( file_exists ( 'sequence/types/add/fixed.php' ) ) {
      $fixed = include 'sequence/types/add/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence add='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>