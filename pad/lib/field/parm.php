<?php

  function padParm ( $fld, $idx, $type ) {

    if ( isset ( $GLOBALS ['padPrm'] [$idx] [$fld] ) )
      if ( $type == 7 )
        return TRUE;
      else
        return $GLOBALS ['padPrm'] [$idx] [$fld];

    return INF;

  }

?>