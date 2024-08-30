<?php

  function padSeqCheckBell ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/bell/bool.php" ) )
      return padSeqBoolBell ( $n );

    $text = padCode ( "{sequence bell, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>