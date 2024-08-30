<?php

  function padSeqCheckNot ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/not/bool.php" ) )
      return padSeqBoolNot ( $n );

    $text = padCode ( "{sequence not, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>