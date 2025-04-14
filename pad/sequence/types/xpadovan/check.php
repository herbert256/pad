<?php

  function padSeqCheckXpadovan ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/xpadovan/bool.php' ) )
      return padSeqBoolXpadovan ( $n, $p );

    if ( file_exists ( 'sequence/types/xpadovan/fixed.php' ) ) {
      $fixed = include 'sequence/types/xpadovan/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/xpadovan/generated.php' ) ) 
      return in_array ( $n, PADxpadovan );

    $text = padCode ( "{sequence xpadovan, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>