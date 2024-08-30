<?php

  function padSeqCheckCubic ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/cubic/bool.php" ) )
      return padSeqBoolCubic ( $n );

    $text = padCode ( "{sequence cubic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>