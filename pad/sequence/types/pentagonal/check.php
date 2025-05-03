<?php

  function pqCheckPentagonal ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/pentagonal/bool.php' ) )
      return pqBoolPentagonal ( $n, $p );

    if ( file_exists ( 'sequence/types/pentagonal/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/pentagonal/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/pentagonal/generated.php' ) ) 
      return in_array ( $n, PADpentagonal );

    #$text = padCode ( "{sequence pentagonal, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence pentagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>