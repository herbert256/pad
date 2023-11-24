<?php

  function padSeqPolite ($n) {

    $n += 1;
    $base = 2;
    return floor ($n + (log(($n + (log($n) /
                 log($base))))) / log($base) );

  }

?>