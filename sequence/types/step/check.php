<?php

  function padSeqCheckStep ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/step/bool.php" ) )
      return padSeqBoolStep ( $n );

    $text = padCode ( "{sequence step, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>