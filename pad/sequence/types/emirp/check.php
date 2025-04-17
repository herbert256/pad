<?php

  function pqCheckEmirp ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/emirp/bool.php' ) )
      return pqBoolEmirp ( $n, $p );

    if ( file_exists ( 'sequence/types/emirp/fixed.php' ) ) {
      $fixed = include 'sequence/types/emirp/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/emirp/generated.php' ) ) 
      return in_array ( $n, PADemirp );

    $text = padCode ( "{sequence emirp, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>