<?php

  function padSeqCheckFloor ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/floor/bool.php" ) )
      return padSeqBoolFloor ( $n );

    $text = padCode ( "{sequence floor, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>