<?php
  
  $padLogPrt = padLogGetLevel ($pad);

  foreach ( $padLogPrt as $padK => $padV )
    $padLogNow [$padK] = $padV;

  if ( $padLogParse ) {

    $padParse = TRUE;

    $padLogNow ['parseTrue']  = padRetrieveContent ( $padTrue  [$pad] );
    $padLogNow ['parseFalse'] = padRetrieveContent ( $padFalse [$pad] );

    $padParse = FALSE;
    
  }

?>