<?php

  function padSeqCheckBell ( $f, $n ) {

    if ( file_exists ( 'sequence/types/bell/bool.php' ) )
      return padSeqBoolBell ( $n );

    if ( file_exists ( 'sequence/types/bell/generated.php' ) ) 
      return in_array ( $n, PADbell );

    if ( file_exists ( 'sequence/types/bell/fixed.php' ) ) {
      $fixed = include 'sequence/types/bell/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence bell, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>