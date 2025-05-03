<?php

  function pqCheckDivide ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/divide/bool.php' ) )
      return pqBoolDivide ( $n, $p );

    if ( file_exists ( 'sequence/types/divide/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/divide/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/divide/generated.php' ) ) 
      return in_array ( $n, PADdivide );

    #$text = padCode ( "{sequence divide='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence divide='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>