<?php

  function padSeqCheckRepeat ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/repeat/bool.php" ) )
      return padSeqBoolRepeat ( $n );

    $text = padCode ( "{sequence repeat, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>