<?php

  function pqCheckAdd ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/add/bool.php' ) )
      return pqBoolAdd ( $n, $p );

    if ( file_exists ( 'sequence/types/add/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/add/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/add/generated.php' ) ) 
      return in_array ( $n, PADadd );

    #$text = padCode ( "{sequence add='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence add='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>