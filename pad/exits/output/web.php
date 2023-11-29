<?php

  $padLen = ( $padStop == 200 ) ? strlen($padOutput) : 0;

  if ( $padStop == '200' and $padWebEtag304 and ($padCacheClientEtag ?? '' ) == $padEtag )
    $padStop = 304;

  padWebHeaders ( $padStop );

  padWebSend ( $padStop );

  padExit ();

?>