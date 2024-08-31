<?php

  function padSeqCheckModulo ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/modulo/bool.php' ) )
      return padSeqBoolModulo ( $n );

    if ( file_exists ( '/pad/sequence/types/modulo/generated.php' ) ) 
      return in_array ( $n, PADmodulo );

    if ( file_exists ( '/pad/sequence/types/modulo/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/modulo/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence modulo, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>