<?php

  function padSeqCheckXpadovan ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/xpadovan/bool.php" ) )
      return padSeqBoolXpadovan ( $n );

    $text = padCode ( "{sequence xpadovan, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>