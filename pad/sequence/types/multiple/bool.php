<?php

  function pqBoolMultiple ( $n, $p=0 ) {

    global $pqParm;

    return ( $n == ceil ( $n / $pqParm) * $pqParm );

  }

?>