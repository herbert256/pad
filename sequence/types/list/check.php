<?php

  function padSeqCheckList ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/list/bool.php" ) )
      return padSeqBoolList ( $n );

    $text = padCode ( "{sequence list, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>