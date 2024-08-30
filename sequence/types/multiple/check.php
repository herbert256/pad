<?php

  function padSeqCheckMultiple ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/multiple/bool.php" ) )
      return padSeqBoolMultiple ( $n );

    $text = padCode ( "{sequence multiple, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>