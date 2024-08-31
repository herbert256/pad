<?php

  function padSeqCheckNor ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/nor/bool.php' ) )
      return padSeqBoolNor ( $n );

    if ( file_exists ( '/pad/sequence/types/nor/generated.php' ) ) 
      return in_array ( $n, PADnor );

    if ( file_exists ( '/pad/sequence/types/nor/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/nor/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence nor, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>