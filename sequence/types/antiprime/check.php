<?php

  function padSeqCheckAntiprime ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/antiprime/bool.php" ) )
      return padSeqBoolAntiprime ( $n );

    $text = padCode ( "{sequence antiprime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>