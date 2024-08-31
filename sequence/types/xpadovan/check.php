<?php

  function padSeqCheckXpadovan ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/xpadovan/bool.php' ) )
      return padSeqBoolXpadovan ( $n );

    if ( file_exists ( '/pad/sequence/types/xpadovan/generated.php' ) ) 
      return in_array ( $n, PADxpadovan );

    if ( file_exists ( '/pad/sequence/types/xpadovan/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/xpadovan/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence xpadovan, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>