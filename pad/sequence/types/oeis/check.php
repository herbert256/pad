<?php

  function pqCheckOeis ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/oeis/bool.php' ) )
      return pqBoolOeis ( $n, $p );

    if ( file_exists ( 'sequence/types/oeis/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/oeis/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/oeis/generated.php' ) ) 
      return in_array ( $n, PADoeis );

    #$text = padCode ( "{sequence oeis='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence oeis='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>