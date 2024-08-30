<?php

  function padSeqCheckSemiprime ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/semiprime/bool.php" ) )
      return padSeqBoolSemiprime ( $n );

    $text = padCode ( "{sequence semiprime, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>