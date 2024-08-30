<?php

  function padSeqCheckAdd ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/add/bool.php" ) )
      return padSeqBoolAdd ( $n );

    $text = padCode ( "{sequence add, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>