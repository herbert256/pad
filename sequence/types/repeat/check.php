<?php

  function padSeqCheckRepeat ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/repeat/bool.php' ) )
      return padSeqBoolRepeat ( $n );

    if ( file_exists ( '/pad/sequence/types/repeat/generated.php' ) ) 
      return in_array ( $n, PADrepeat );

    if ( file_exists ( '/pad/sequence/types/repeat/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/repeat/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence repeat, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>