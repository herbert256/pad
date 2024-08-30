<?php

  function padSeqCheckPull ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/pull/bool.php" ) )
      return padSeqBoolPull ( $n );

    $text = padCode ( "{sequence pull, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>