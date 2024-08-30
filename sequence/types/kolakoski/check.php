<?php

  function padSeqCheckKolakoski ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/kolakoski/bool.php" ) )
      return padSeqBoolKolakoski ( $n );

    $text = padCode ( "{sequence kolakoski, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>