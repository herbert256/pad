<?php

  function padParm ($fld, $idx) {

    global $pad;
        
    if ( isset ( $GLOBALS ['padPrm'] [$idx] [$fld] ) )
      return $GLOBALS ['padPrm'] [$idx] [$fld];

    return INF;

  }

?>