<?php

  function padSeqCheckGould ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/gould/bool.php" ) )
      return padSeqBoolGould ( $n );

    $text = padCode ( "{sequence gould, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>