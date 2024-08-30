<?php

  function padSeqCheckStrong ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/strong/bool.php" ) )
      return padSeqBoolStrong ( $n );

    $text = padCode ( "{sequence strong, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>