<?php

  if ( $padResult [$pad] or ! $padHstShort )  
    $padHstPnt [$pad] [ 'result' ] = $padResult[$pad];

  if ( ! $padHstShort )  
    return;
  
  if ( isset ( $padHstPnt [$pad] ['occurrences'] ) and count ( $padHstPnt [$pad] ['occurrences'] )  == 1 ) {

    $padHstFirst = array_key_first ( $padHstPnt [$pad] ['occurrences'] );

    foreach ( $padHstPnt [$pad] ['occurrences'] [$padHstFirst] as $padK => $padV )
      $padHstPnt [$pad] [$padK] = $padV;

    unset ( $padHstPnt [$pad] ['occurrences'] );

  }

?>