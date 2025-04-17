<?php

  function pqCheckLucky ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/lucky/bool.php' ) )
      return pqBoolLucky ( $n, $p );

    if ( file_exists ( 'sequence/types/lucky/fixed.php' ) ) {
      $fixed = include 'sequence/types/lucky/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/lucky/generated.php' ) ) 
      return in_array ( $n, PADlucky );

    $text = padCode ( "{sequence lucky, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>