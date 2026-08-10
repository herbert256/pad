<?php

  // Reads one parameter of the tag at level $idx straight out of $padPrm, for the option
  // lookups padField routes here (types 5 and 6, and the retries when a plain field name
  // turned up nothing). Returns INF when the tag was not given that parameter - the
  // caller turns that into '' or FALSE.

  function padParm ( $fld, $idx, $type ) {

    global $padPrm;

    if ( isset ( $padPrm [$idx] [$fld] ) ) {

      padDoneAt ( $idx, $fld );

      if ( $type == 7 )
        return TRUE;
      else
        return $padPrm [$idx] [$fld];

    }

    return INF;

  }

?>