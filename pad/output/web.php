<?php

  $padLen = ( $padStop == 200 ) ? strlen($padOutput) : 0;

  if ( $padStop == '200' and $padWebEtag304 )
    if ( ( $padCacheClient ?? '' ) == $padEtag ) {
      $padStop = 304;
      $padStop = $padStop;
    }

  padWebHeaders ( $padStop );

  padWebSend ( $padStop );

  padExit ();

?>