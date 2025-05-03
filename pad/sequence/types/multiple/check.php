<?php

  function pqCheckMultiple ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/multiple/bool.php' ) )
      return pqBoolMultiple ( $n, $p );

    if ( file_exists ( 'sequence/types/multiple/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/multiple/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/multiple/generated.php' ) ) 
      return in_array ( $n, PADmultiple );

    #$text = padCode ( "{sequence multiple='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence multiple='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>