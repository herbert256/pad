<?php

  function padSeqCheckEnneadecagonal ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/enneadecagonal/bool.php" ) )
      return padSeqBoolEnneadecagonal ( $n );

    $text = padCode ( "{sequence enneadecagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>