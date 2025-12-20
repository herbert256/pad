<?php

  if ( padStartAndClose ('next') )
    return TRUE;

  $padWhile = padEvalBool ( $padParms [$pad] [0] ['padPrmOrg'] );

  if ($padTag [$pad] == 'while') {

    $padWalk [$pad] = (   $padWhile ) ? 'next' : '';
    return            (   $padWhile ) ? [ 1 => [] ] : NULL;

  } else {

    $padWalk [$pad] = ( ! $padWhile ) ? 'next' : '';
    return            ( ! $padWhile ) ? [ 1 => [] ] : NULL;

  }

?>