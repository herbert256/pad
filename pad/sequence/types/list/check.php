<?php

  function pqCheckList ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/list/bool.php' ) )
      return pqBoolList ( $n, $p );

    if ( file_exists ( 'sequence/types/list/fixed.php' ) ) {
      $fixed = include 'sequence/types/list/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/list/generated.php' ) ) 
      return in_array ( $n, PADlist );

    $text = padCode ( "{sequence list, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>