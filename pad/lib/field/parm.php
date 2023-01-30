<?php

  function padParm ($fld, $type=0) {

    global $pad;
        
    if ( strpos($fld, '#') === FALSE )
      $tag = '';
    else {
      $tmp = padExplode ($fld, '#', 2);
      $tag = $tmp[0];
      $fld = $tmp[1];
    }

    if ( ! $tag and isset ( $GLOBALS ['padPrm'] [$pad] [$fld] ) )
      return $GLOBALS ['padPrm'] [$pad] [$fld];

    if ( ! $tag )
      if ( $type == 1 )
        $idx = $GLOBALS ['pad'] - 1;
      else
        $idx = padFieldFirstNonParm  ();
    else
      $idx = padFieldGetLevel ($tag);

    if ( isset ( $GLOBALS ['padPrm'] [$idx] [$fld] ) )
      return $GLOBALS ['padPrm'] [$idx] [$fld];

    return '';

  }

?>