<?php

  function padSeqCheckJuggler ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/juggler/bool.php" ) )
      return padSeqBoolJuggler ( $n );

    $text = padCode ( "{sequence juggler, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>