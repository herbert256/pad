<?php

    if ( ! padValidVarAt ($padFld) )
      return padIgnore ( "@ Field '$padFld' not a valid name" );

    $padVal = padAt ( $padFld, 'var' );

    if ( ! in_array('noError', $padOpts) and $padVal === INF )
      padError ("Field '$padFld' not found");

    if ( $padVal === INF )
      $padVal = '';
 
    return $padVal;
    
?>