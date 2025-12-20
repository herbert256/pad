<?php

  function padParm ( $fld, $idx, $type ) {

    global $padPrm;

    if ( isset ( $padPrm [$idx] [$fld] ) )
      if ( $type == 7 )
        return TRUE;
      else
        return $padPrm [$idx] [$fld];

    return INF;

  }

?>