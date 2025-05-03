<?php

  function pqCheckAnd ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/and/bool.php' ) )
      return pqBoolAnd ( $n, $p );

    if ( file_exists ( 'sequence/types/and/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/and/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/and/generated.php' ) ) 
      return in_array ( $n, PADand );

    #$text = padCode ( "{sequence and='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence and='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>