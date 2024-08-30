<?php

  function padSeqCheckPolite ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/polite/bool.php" ) )
      return padSeqBoolPolite ( $n );

    $text = padCode ( "{sequence polite, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>