<?php

  function padSeqCheckNot ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/not/bool.php' ) )
      return padSeqBoolNot ( $n, $p );

    if ( file_exists ( 'sequence/types/not/generated.php' ) ) 
      return in_array ( $n, PADnot );

    if ( file_exists ( 'sequence/types/not/fixed.php' ) ) {
      $fixed = include 'sequence/types/not/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence not, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>